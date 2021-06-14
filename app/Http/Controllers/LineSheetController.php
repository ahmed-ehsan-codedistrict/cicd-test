<?php

namespace App\Http\Controllers;

use App\Models\LineSheetGroup;
use App\Models\LineSheetGroupProducts;
use Illuminate\Http\Request;
use App\Models\LineSheets;
use App\Rules\checkUnqiueValuOneUpdate;
use App\Rules\IsValueEmpty;
use PhpParser\Node\Scalar\MagicConst\Line;
use App\User;
use Illuminate\Validation\Rule;
use App\Utilities\Helpers;
use PHPUnit\Framework\Constraint\IsEmpty;
use App\Models\LineSheetShare;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class LineSheetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAll(Request $request)
    {
        try {

            $brands =  user::userBrands();
            $params = (array) json_decode($request->getContent(), true);

            $userId =  Auth::id();
            $companyNo =  Auth::user()->CompanyNo;
            $specificUser =  isset($params['specificUser']) ? $params['specificUser'] : false;

            $lineSheetsListing =  LineSheets::lineSheetListing($params, $brands, $userId, $companyNo,  $specificUser);

            return response()->json(['response' => $lineSheetsListing, 'success' => true], 200);
        } catch (\Throwable $th) {
            return response()->json(["message" => "oop! something went wrong.", "error" => $th->getMessage()], 403);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->isRequestValidate($request);
        $userId =  Auth::id();
        $companyNo =  Auth::user()->CompanyNo;


        // set date format
        $startDate =  Helpers::changeDateFormat($request->startDate, 'Y-m-d');
        $endDate =  Helpers::changeDateFormat($request->endDate, 'Y-m-d');


        try {
            $isUpdated = null;
            // Update the line sheet
            if (isset($request->id) && $request->id > 0) {

                $isUpdated  = LineSheets::updateLineSheet(
                    $request->id,
                    $request->lineSheetName,
                    $userId,
                    $request->customerId,
                    $request->brand,
                    $request->bannerPath,
                    $startDate,
                    $endDate,
                    $request->visibility,
                    $request->status,
                    $request->templateId,
                    $request->Division
                );
            }
            $LineSheets = null;
            // Add new Line Sheet
            if (isset($request->id) && $request->id == 0) {

                $LineSheets =  LineSheets::createLineSheet(
                    $request->id,
                    $request->lineSheetName,
                    $userId,
                    $request->customerId,
                    $request->brand,
                    $request->bannerPath,
                    $startDate,
                    $endDate,
                    $request->visibility,
                    $request->status,
                    $request->templateId,
                    $request->Division
                );
            }

            $linesheetID = empty($request->id) ? ($LineSheets->id) : $request->id;
            //checking
            $linesheet = LineSheets::select(
                'LineSheets.id',
                'LineSheets.lineSheetName',
                'LineSheets.companyNo',
                'LineSheets.customerId',
                'LineSheets.templateId',
                'LineSheets.startDate',
                'LineSheets.endDate',
                'LineSheets.bannerPath',
                'LineSheets.brand',
                DB::raw("
                        trim(cs.FLNM2S) as customerName,
                        FORMAT (LineSheets.created_at, 'MM/dd/yyyy') as created_at,
                        FORMAT (LineSheets.updated_at, 'MM/dd/yyyy') as updated_at
                    "),
                "op2.DisplayValue as isArchived",
                "op1.DisplayValue as status",
                "op.DisplayValue as lsVisibility",
                "u.name as createdBy",
                "u.name as lsCreatedBy",
                "LineSheets.Division as Division",
                "LineSheets.createdBy as createdById"
            );

            $linesheet = $linesheet->join("users as u", "u.id", "=", "LineSheets.createdBy")
                ->leftJoin("CUSTMS0 as cs", function ($join) use ($companyNo) {
                    $join->on("cs.CustomerNo", "=", "LineSheets.customerId");
                    $join->where("cs.CompanyNo", "=", $companyNo);
                });

            $linesheet = $linesheet->Join('Options as op', function ($join) use ($companyNo) {
                $join->on('op.DisplayID', '=', 'LineSheets.visibility')
                    ->where('op.TableName', '=', "LineSheets")
                    ->where('op.TableColumn', '=', "visibility")
                    ->where('op.CompanyNo', '=', $companyNo);
            });
            $linesheet = $linesheet->Join('Options as op1', function ($join) use ($companyNo) {
                $join->on('op1.DisplayID', '=', 'LineSheets.status')
                    ->where('op1.TableName', '=', "LineSheets")
                    ->where('op1.TableColumn', '=', "status")
                    ->where('op1.CompanyNo', '=', $companyNo);
            });
            $linesheet = $linesheet->Join('Options as op2', function ($join) use ($companyNo) {
                $join->on('op2.DisplayID', '=', 'LineSheets.isArchived')
                    ->where('op2.TableName', '=', "LineSheets")
                    ->where('op2.TableColumn', '=', "isArchived")
                    ->where('op2.CompanyNo', '=', $companyNo);
            });

            $linesheet = $linesheet->where("LineSheets.id", $linesheetID)->get();
            if (LineSheetShare::where('ShareTo', Auth::id())->where('LineSheetId', $linesheetID)->exists()) {
                $linesheet[0]->lsVisibility = "Shared";
            }
            if ($isUpdated || $LineSheets) {

                return $linesheet[0];
            }

            return response()->json(["message" => "Something went wrong", "error" => "Linesheet not created"], 400);
        } catch (\Throwable $th) {
            return response()->json(['message' => "Something went wrong", 'error' => $th->getMessage()], 400);
        }
    }


    /**
     * change the LineSheet Status, Visibility Stauts and Archived Status
     * @param $request
     * @return array
     */


    public function changeLineSheetStatus(Request $request)
    {
        try {
            $this->isRequestValidate($request, "status");

            if ($request->type == "visibility") {
                $column = "visibility";
            }

            if ($request->type == "status") {
                $column = "status";
            }

            if ($request->type == "archived") {
                $column = "isArchived";
                if (LineSheets::where('createdBy', Auth::id())->where('id', $request->lineSheetId)->count() <= 0) {
                    return response()->json(['message' => "Only Owner can archived the linesheet", "success" => false], 400);
                }
            }

            $response = LineSheets::updateLineSheetStatus($request->value, $request->lineSheetId, $column);

            if ($response) {

                return response()->json(["response" => "Line Sheet has been updated successfully.", "success" => true]);
            }

            return response()->json(["message" => "Something went wrong.", "error" => $response], 403);
        } catch (\Throwable $th) {
            return response()->json(["message" => "Something went wrong.", "error" => $th->getMessage()], 403);
        }
    }



    // Line Sheet Duplicate records
    public function duplicateLineSheet(Request $request)
    {
        try {
            $this->isRequestValidate($request, "duplicate");
            DB::beginTransaction();

            $newLineSheetId = LineSheets::duplicateLineSheet($request->lineSheetId, Auth::id());
            if ($newLineSheetId > 0) {
                $Groups = LineSheetGroup::where('LineSheetId', $request->lineSheetId)->get();
                $productArr = [];
                //Copy Group
                foreach ($Groups as $gr) {
                    $oldGroupId = $gr->id;
                    $newGroupId = LineSheetGroup::duplicateGroup($oldGroupId, $newLineSheetId);
                    $groupProducts = LineSheetGroupProducts::where('GroupId', $oldGroupId)->get();
                    //Copy Products
                    foreach ($groupProducts as $gp) {
                        $productArr[] = [
                            "LinesheetId" => $newLineSheetId,
                            "ProductId" => $gp->ProductId,
                            "ColorId" => $gp->ColorId,
                            "GroupId" => $newGroupId,
                            "PublicNotes" => $gp->PublicNotes,
                            "PublicNotesCreatedBy" => Auth::id(),
                            "CreatedBy" => Auth::id(),
                            "CompanyNo" => Auth::user()->CompanyNo,
                            "created_at" => Carbon::now(),
                            "updated_at" => Carbon::now()
                        ];
                    }
                }
                if (count($productArr) > 0) {
                    LineSheetGroupProducts::bulkAddProducts($productArr);
                }
                DB::commit();
                return response()->json(["response" => "Line Sheet has been duplicated successfully.", "success" => true]);
            }

            DB::commit();
            return response()->json(["message" => "Something went wrong.", "error" => $newLineSheetId], 403);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(["message" => "Something went wrong.", "error" => $th->getMessage()], 403);
        }
    }


    /**
     * Line Sheet Share function
     * @param $request
     * @return array
     */

    public function lineSheetShare(Request $request)
    {

        try {
            $this->isRequestValidate($request, "share");

            if (LineSheets::where('createdBy', Auth::id())->where('id', $request->lineSheetId)->exists()) {

                if (in_array(Auth::id(), $request->ShareTo)) {
                    return response()->json(['message' => "Can't Share linesheet with yourself", "success" => false], 400);
                }

                LineSheetShare::where('ShareBy', Auth::id())->where('LineSheetId', $request->lineSheetId)->delete();

                $records =  [];
                foreach ($request->ShareTo as $rs) {

                    $records[] = [
                        "ShareBy" =>  Auth::id(),
                        "ShareTo" =>  $rs,
                        "LineSheetId" =>  $request->lineSheetId,
                        "created_at" =>  Carbon::now(),
                        "updated_at" =>  Carbon::now(),
                        "CompanyNo" => Auth::user()->CompanyNo
                    ];
                }
                if (count($records) <= 0) {
                    return response()->json(['message' => "Linesheet has been removed from share list", "success" => true], 200);
                }

                $response = LineSheetShare::bulkLineSheetShare($records);

                if ($response) {
                    return response()->json(['message' => "Line Sheet shared successfully.", "success" => true], 200);
                }
                return response()->json(['message' => "Something went wrong", 'error' => $response], 403);
            } else {
                return response()->json(['message' => "Only Owner can share the linesheet", "success" => false], 400);
            }
        } catch (\Throwable $th) {

            return response()->json(['message' => "Something went wrong", 'error' => $th->getMessage()], 403);
        }

        return $request;
    }


    /**
     * check that request is validated or not
     */

    public function isRequestValidate(Request $request,  $fnCallOrigin = "lsCrud")
    {

        // validation for LineSheet Update and Create
        if ($fnCallOrigin == "lsCrud") {

            if (isset($request->id) && $request->id == 0) {

                // add validation to request
                $request->validate([
                    "lineSheetName" =>  ['required', 'string'],
                    "brand" => "string|required"
                ]);
            }

            if (isset($request->id) && $request->id > 0) {

                return  $request->validate([
                    "lineSheetName" =>  [
                        'required',
                        'string',
                        new checkUnqiueValuOneUpdate($request->id, Auth::user()->CompanyNo, Auth::id())
                    ],
                    "brand" => "string|required"
                ]);
            }
        }

        // validation for LineSheet Staus JSON

        if ($fnCallOrigin == "status") {

            return  $request->validate([
                "type" => ["required"],
                "value" => ["required"],
                "lineSheetId" => ["required"]
            ]);
        }

        //validate for LineSheet Duplicate
        if ($fnCallOrigin == "duplicate") {

            $request->validate([
                "lineSheetId" => ["required"]
            ]);
        }

        //LineSheet Share
        if ($fnCallOrigin == "share") {

            $request->validate([
                'lineSheetId' => 'required',
                'ShareTo' => 'array'
            ]);
        }
    }

    public function getUserLineSheet()
    {
        return response()->json(LineSheets::getUserLineSheets(Auth::id()));
    }

    public function getLinesheet(Request $request)
    {
        $request->validate([
            'LineSheetId' => 'required'
        ]);
        try {
            return response()->json(LineSheets::getLinesheet($request->LineSheetId));
        } catch (\Error $e) {
            return response()->json(['message' => "Something went wrong", 'error' => $e->getMessage()]);
        }
    }

    public function setImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg', //|max:2048',
            'path' => 'required',
            'linesheetId' => 'integer|nullable'
        ]);
        try {
            if (LineSheets::select('bannerPath')->where('id', '=', $request->imageName)->where('bannerPath', 'like', '%' . $request->imageName . '.' . '%')->exists()) {
                $path = LineSheets::select('bannerPath')->where('id', '=', $request->imageName)->where('bannerPath', 'like', '%' . $request->imageName . '.' . '%')->get();
                $image_path = $path[0]->bannerPath;
                if (File::exists($image_path)) {
                    File::delete($image_path);
                }
            }
            $bannerPath = Helpers::storeFile($request);
            if ($request->linesheetId) {
                LineSheets::updatePath($bannerPath, $request->linesheetId);
                return response()->json(LineSheets::getLinesheet($request->linesheetId));
            } else {
                return $bannerPath;
            }
        } catch (\Error $e) {
            return response()->json(['message' => "Something went wrong", 'error' => $e->getMessage()], 400);
        }
    }

    public function createNewLinesheet(Request $request)
    {
        try {
            $request->validate([
                'LinesheetName' => 'required|string',
                'GroupName' => 'required|string',
                'Brand' => 'required|string|exists:ProdPLM,Brand',
                'Products.*.ProductId' => 'string|required|exists:PRHDMS0,Style',
                'Products.*.ColorId' => 'string|required|exists:PRDTMS0,Color'
            ]);
            $LinesheetName = $request->all()['LinesheetName'];
            $GroupName = $request->all()['GroupName'];
            $Brand = $request->all()['Brand'];
            $products = $request->all()['Products'];

            $linesheet = LineSheets::createLineSheet(
                0,
                $LinesheetName,
                Auth::id(),
                null, //CustomerId
                $Brand,
                null,
                date('Y-m-d'),
                null,
                0, //Visibility
                1, // Status
                0, //TemplateId
                null
            );

            $group = LineSheetGroup::createGroup($GroupName, $linesheet['id']);
            $linesheetProducts = [];
            $products = Helpers::array_multi_unique($products);
            foreach ($products as $p) {
                $linesheetProducts[] = ['ProductId' => $p['ProductId'], 'ColorId' => $p['ColorId'], 'GroupId' => $group['id'], 'CompanyNo' => Auth::user()->CompanyNo, 'CreatedBy' => Auth::id(), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now(), "DisplayOrder" => null];
            }
            LineSheetGroupProducts::insert($linesheetProducts);
            return response()->json(['LinesheetId' => strval($linesheet['id']), 'GroupId' => strval($group['id'])], 200);
        } catch (\Throwable $th) {
            return response()->json(["message" => "Something went wrong.", 'error' => $th->getMessage()], 403);
        }
    }
}
