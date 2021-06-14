<?php

namespace App\Http\Controllers;

use App\Models\CustomSort;
use App\Models\Workspaces;
use App\Models\WorkspaceColors;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PRHDMS0;
use App\Models\PRDTMS0;
use Illuminate\Support\Facades\Validator;
use App\Models\LineSheetGroup;
use App\Models\LineSheets;
use App\Models\LineSheetGroupProducts;
use Illuminate\Support\Carbon;

class WorkspaceController extends Controller
{


    public function create(Request $request)
    {
        $request->validate([
            '*.ProductId' => 'required|exists:PRHDMS0,Style',
            '*.ColorId' => 'required|array',
            '*.ColorId.*' => 'required|exists:COLRMS0,Color',
            '*.Type' => 'required|in:linesheet,product'
        ]);
        try {
            $workspaceRequestArray = $request->all();
            if ($workspaceRequestArray) {
                Workspaces::upsertWorkspaces($workspaceRequestArray);
                return response()->json(['response' => "Workspace saved successfully"], 200);
            } else {
                return response()->json(['response' => "Something went wrong", 'message' => "Invalid Data Entered"], 403);
            }
        } catch (\Error $e) {
            return response()->json(['message' => "Something went wrong", 'response' => $e->getMessage()], 400);
        }
    }

    public static function deleteWorkspace($id)
    {
        Workspaces::deleteWorkspaceProduct($id);
        WorkspaceColors::deleteWorkspace($id);
    }

    public function deleteSelectedProducts(Request $request)
    {
        //validating inputs
        $request->validate([
            'ProductId' => [
                'string|required|exists:PRHDMS0,Style'
            ],
            'Type' => [
                'string|required'
            ],
        ]);
        try {
            foreach ($request->all() as $r) {
                if (Workspaces::where('UserId', Auth::id())->where('ProductId', $r['ProductId'])->where('CompanyNo', Auth::user()->CompanyNo)->where('Type', $r['Type'])->exists()) {
                    $id = Workspaces::where('UserId', Auth::id())->where('ProductId', $r['ProductId'])
                        ->where('Type', $r['Type'])
                        ->where('CompanyNo', Auth::user()->CompanyNo)->pluck('Id');
                    WorkspaceController::deleteWorkspace($id);
                }
            }
            return response()->json(['message' => "Deleted Succesfully"]);
        } catch (\Error $e) {
            return response()->json(['message' => "Something went wrong", 'error' => $e->getMessage()], 400);
        }
    }

    public function deleteAll(Request $request)
    {
        //validating inputs
        $request->validate([
            'Type' => 'string|required'
        ]);
        try {
            $del = Workspaces::where("UserId", Auth::id()) //Auth::User
                ->where("Type", $request->Type)
                ->where('CompanyNo', Auth::user()->CompanyNo)->get();
            foreach ($del as $d) {
                $id = $d['Id'];
                WorkspaceController::deleteWorkspace($id);
            }
            return response()->json(['message' => "Deleted Succesfully"]);
        } catch (\Error $e) {
            return response()->json(['message' => "Something went wrong", 'error' => $e->getMessage()], 400);
        }
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Workspace  $workspace
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $request->validate([
            'Type' => 'string|required|in:product,linesheet'
        ]);
        try {
            return Workspaces::showAll($request->Type);
        } catch (\Error $e) {
            return response()->json(['message' => "Something went wrong", 'error' => $e->getMessage()], 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Workspace  $workspace
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $request->validate([
            'Type' => 'string|required',
            'ProductId' => 'string|required|exists:PRHDMS0,PRCD3K|exists:Workspaces,ProductId',
            'ColorId' => 'required|array'
        ]);
        $ws = Workspaces::where("UserId", Auth::id())
            ->where("Type", $request->Type)
            ->where("ProductId", $request->ProductId)->pluck('Id');

        foreach ($request->ColorId as $color) {
            $arr[] = array("WorkspaceId" => $ws[0], "ColorId" => $color, "CompanyNo" => Auth::user()->CompanyNo);
        }
        if ($ws) {
            WorkspaceColors::deleteWorkspace($ws[0]);
            $workspaceModel = new Workspaces;
            $workspaceModel->Id = $ws[0];
            $workspaceModel->colors()->sync($arr);
        }
    }

    //move to different group
    public function moveToDifferentGroup(Request $request)
    {

        try {
            // add validation to request
            $validator = validator()->make(request()->all(), [
                '*.ProductId' => 'string|required',
                '*.GroupId' =>  'required',
                '*.ColorId' => 'required',
                '*.LineSheetId' => 'required',
                '*.MoveTo' => 'required'
            ]);

            if ($validator->fails()) {
                return $validator->errors();
            }
            if (count($request->all()) > 0) {
                if (LineSheets::canLineSheetEdit($request[0]['LineSheetId'])) {

                    $records = [];
                    $deleteRecords = array();
                    $loopIteration = 0;
                    foreach ($request->all() as $r) {

                        $ColorId = $r['ColorId'];
                        $ProducId = $r['ProductId'];
                        $LineSheetId =  $r['LineSheetId'];

                        $records[] = [
                            "LinesheetId" =>  $LineSheetId,
                            "ProductId" => $ProducId,
                            "ColorId" => $ColorId,
                            "GroupId" => $r['GroupId'],
                            "CreatedBy" => Auth::id(),
                            "CompanyNo" => Auth::user()->CompanyNo,
                            "created_at" => Carbon::now()
                        ];


                        if ($loopIteration == 0) {
                            $ProductDelete = LineSheetGroupProducts::where(function ($query) use ($ColorId, $ProducId, $LineSheetId) {
                                $query->where("ProductId", $ProducId)
                                    ->where("ColorId", $ColorId)
                                    ->where("LinesheetId", $LineSheetId);
                            });
                        } else {
                            $ProductDelete = $ProductDelete->orWhere(function ($query) use ($ColorId, $ProducId, $LineSheetId) {
                                $query->where("ProductId", $ProducId)
                                    ->where("ColorId", $ColorId)
                                    ->where("LinesheetId", $LineSheetId);
                            });
                        }

                        $loopIteration++;
                    }
                    //copy the object
                    $ProductRecords = $ProductDelete;
                    $ProductRecords = $ProductRecords->get();
                    $DeletedItemsArr =  [];
                    foreach ($ProductRecords as $v) {
                        array_push($DeletedItemsArr, $v->id);
                    }
                    //delete from custom sort table
                    CustomSort::whereIn("LSGPId", $DeletedItemsArr)->delete();

                    //Delete product from linesheet
                    $ProductDelete = $ProductDelete->delete();

                    $response =  LineSheetGroupProducts::bulkAddProducts($records);

                    if ($response) {
                        return response()->json(['message' => 'Products added successfully', 'success' => true], 200);
                    }
                    return response()->json(["message" => "oops! something went wrong", 'success' => $response], 403);
                } else {
                    return response()->json(['message' => "You don't have rights to add products", 'success' => false, 'error' => " You don't have rights to add products"], 400);
                }
            } else {
                return response()->json(['message' => "Please select atleast one product to add in a group", 'success' => false, 'error' => "Empty Array"], 400);
            }
        } catch (\Throwable $th) {
            return response()->json(["message" => "Something went wrong", 'error' => $th->getMessage()], 403);
        }
    }
}
