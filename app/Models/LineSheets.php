<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Scalar\MagicConst\Line;
use App\Utilities\Helpers;
use App\Models\LineSheetShare;
use App\User;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Types\Parent_;
use Illuminate\Validation\Rule;

class LineSheets extends BaseModel
{
    /**
     * The table use against this model
     *
     * @var string
     * Table Name : LineSheets
     */
    public $table = 'LineSheets';
    /**
     * The Primary key used withing table
     *
     * @var string
     */
    public $primaryKey = 'id';

    /**
     * Primary key is not incremented
     *
     * @var boolean
     */
    public $incrementing = true;


    /**
     * Attributes need to be filled
     */
    protected $fillable = [
        'isArchived', 'companyNo', 'lineSheetName', 'customerId', 'bannerPath',
        'brand', 'startDate', 'endDate', 'visibility', 'status', 'createdBy', 'updatedBy',
        'templateId', 'Division'
    ];



    /** RelationShips */

    public function User()
    {
        return $this->belongsTo('App\User', 'createdBy', 'id');
    }

    public function groups()
    {
        return $this->hasMany(LineSheetGroup::class, 'LineSheetId', 'id');
    }


    /** Custom Functions */

    /**
     * create a new LineSheet
     * @return array
     */

    public static function createLineSheet(
        $id = 0,
        $lineSheetName = '',
        $userId,
        $customerId,
        $brand,
        $bannerPath,
        $startDate,
        $endDate,
        $visibility,
        $status,
        $templateId,
        $Division
    ) {
        try {
            if (isset($id) && $id == 0) {

                //RollBack All Db Changes if there is any exception or error

                DB::beginTransaction();

                $LineSheets = LineSheets::Create([
                    "lineSheetName" => $lineSheetName,
                    "createdBy" => $userId,
                    "customerId" => $customerId,
                    "brand" => $brand,
                    "bannerPath" => $bannerPath,
                    "startDate" => $startDate,
                    "endDate" =>  $endDate,
                    "visibility" => $visibility,
                    "status" => $status,
                    "templateId" => $templateId,
                    "Division" => $Division
                ]);

                DB::commit();

                return $LineSheets;
            }
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    /**
     * update a LineSheet
     * @return array
     */

    public static function updateLineSheet(
        $id = 0,
        $lineSheetName = '',
        $userId,
        $customerId,
        $brand,
        $bannerPath,
        $startDate,
        $endDate,
        $visibility,
        $status,
        $templateId,
        $Division

    ) {

        try {

            // Update the time sheet
            if (isset($id) && $id > 0) {

                $LineSheets  = LineSheets::find($id);
                if (LineSheets::where('createdBy', Auth::id())->where('id', $id)->count() > 0) {
                    $LineSheets->lineSheetName =  $lineSheetName;
                }
                $LineSheets->customerId =  $customerId;
                $LineSheets->updatedBy =  $userId;
                $LineSheets->bannerPath =  $bannerPath;
                $LineSheets->brand =  $brand;
                $LineSheets->startDate =  $startDate;
                $LineSheets->endDate =  $endDate;
                if ($visibility != 2) {
                    $LineSheets->visibility =  $visibility;
                }
                $LineSheets->status =  $status;
                $LineSheets->templateId =  $templateId;
                $LineSheets->Division =  $Division;
                DB::beginTransaction();

                $isUpdated =  $LineSheets->save();
                DB::commit();

                return true;
            }
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }


    // get all lineSHeet
    public static function lineSheetListing(array $filters = null, $brands = null, $userId = 0, $companyNo = 0, $specificUser = false)
    {
        $pageNo = $filters['pageNumber'];
        $recordsPerPage = $filters['recordsPerPage'];
        $isArchived =  $filters['isArchived'];
        $offset =  $pageNo * $recordsPerPage;

        $LoginUserShareLineSheet = User::userShareLinesheet();
        // fetch the linesheets created by loggedIn User
        $lineSheets =  LineSheets::getQuery($companyNo, $userId, $filters, false,  $isArchived, $brands,  $LoginUserShareLineSheet, $specificUser);

        /**
         * if 2 exist in id array then
         * show all the linesheet of the login user
         * with shared linesheet
         */


        if (
            !isset($filters['Filter']['visibility'])
            ||
            (isset($filters['Filter']['visibility']['id'])
                &&
                in_array(2, $filters['Filter']['visibility']['id']))
        ) {
            // Union Query to fetch the Shared LineSheets as well
            //--------------------------------------------------
            unset($filters['Filter']['visibility']);
            $shareLineSheets =  LineSheets::getQuery($companyNo, $userId, $filters, true, $isArchived, $LoginUserShareLineSheet, $specificUser);

            $lineSheets = $shareLineSheets->union($lineSheets);
        }

        /**
         * use query that contains the join with LineSheetShare table
         * run the query if the given parameters are true
         */

        if (
            isset($filters['Filter']['visibility'])
            &&
            isset($filters['Filter']['visibility']['id'])
            &&
            count($filters['Filter']['visibility']['id']) == 1
            &&
            $filters['Filter']['visibility']['id'][0] == 2
        ) {
            //  Query to fetch the Shared LineSheets
            //--------------------------------------------------
            unset($filters['Filter']['visibility']);
            $shareLineSheets =  LineSheets::getQuery($companyNo, $userId, $filters, true, $isArchived, $LoginUserShareLineSheet, $specificUser);
            $lineSheets = $shareLineSheets;
        }

        //get sql
        $lineSheetsSql = $lineSheets->toSql();

        // offset and limit is not working with union , make a sql and then merge the union query
        $ContentQuery = DB::table(DB::raw("($lineSheetsSql) as tmp"))->mergeBindings($lineSheets);

        //Apply the Sort By filter
        if (isset($filters['SortBy']) && count($filters['SortBy']) > 0) {

            $filters['SortBy'] = Helpers::sortAssociateArr($filters['SortBy'], 'level', 'asc');
            $ContentQuery = Helpers::addDynamicSortBy($ContentQuery, $filters['SortBy']);
        }



        $lineSheets =  $ContentQuery->offset($offset)
            ->limit($recordsPerPage)
            ->get();

        return $lineSheets;
    }

    /**  change the ls status */
    public static function updateLineSheetStatus($value = 0,  $lsId = 0, $column = "")
    {

        try {
            DB::beginTransaction();

            $lineSheet = LineSheets::where("id", $lsId)->update([$column => $value]);

            DB::commit();

            return $lineSheet;
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }


    /** duplicate the line Sheet */

    public static function duplicateLineSheet($lineSheetId = 0, $userId = 0)
    {

        try {

            DB::beginTransaction();

            $lineSheet = LineSheets::find($lineSheetId);

            $newLineSheet = $lineSheet->replicate();
            $name =  explode(" - Copy", $newLineSheet->lineSheetName);
            $search = $name[0];

            $lineSheetCount = LineSheets::where("lineSheetName", "like", "%$search%")->count();
            if ($lineSheetCount > 0) {

                $newLineSheet->lineSheetName = $name[0] . " - Copy" . ++$lineSheetCount;
            } else {
                $newLineSheet->lineSheetName =  $newLineSheet->lineSheetName . " - Copy2";
            }

            $newLineSheet->createdBy  = $userId;

            $response =  $newLineSheet->save();

            DB::commit();

            return $newLineSheet->id;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th->getMessage();
        }
    }


    /**
     * function used to prepare the query
     */

    public static function getQuery($companyNo = 0, $loggedInUser = 0, array $filters = null, $union = false, $isArchived =  1, $brands = null, $shareLinesheets = null, $specificUser = false)
    {

        $lineSheets = DB::table("LineSheets")
            ->select(
                "LineSheets.id as lsId",
                "LineSheets.lineSheetName as lsName",
                "LineSheets.bannerPath as lsImage",
                DB::raw("
                        (
                        case
                            when LineSheets.status = 1 then 'Active'
                            when LineSheets.status = 0 then 'Inactive'

                        end
                        ) as lsStatus,

                        FORMAT (LineSheets.created_at, 'MM/dd/yyyy') as lsCreatedDate,
                        LineSheets.created_at
                    "),
                "u.name as lsCreatedBy",
                "LineSheets.createdBy as createdById",
                "cs.FLNM2S as lsCustomerName",
                "LineSheets.brand"
            );

        if ($union) {
            $lineSheets = $lineSheets->addSelect(DB::raw("
                    'Shared' as lsVisibility"));
        } else {
            $lineSheets = $lineSheets->addSelect(DB::raw("
                         (
                            case
                            when LineSheets.visibility=1 then 'Private'
                            when LineSheets.visibility=0 then 'Public'
                            end
                        ) as lsVisibility"));
        }
        // put this join if the union is true
        // if the user wants to see the shared linesheet
        if ($union) {

            $lineSheets =  $lineSheets->join("LineSheetShare as lss", function ($join) use ($companyNo) {
                $join->on("lss.LineSheetId", "=", "LineSheets.id");
                $join->where("lss.CompanyNo", "=", $companyNo);
            });
        }
        $lineSheets = $lineSheets->join("users as u", "u.id", "=", "LineSheets.createdBy")
            ->leftJoin("CUSTMS0 as cs", function ($join) use ($companyNo) {
                $join->on("cs.CustomerNo", "=", "LineSheets.customerId");
                $join->where("cs.CompanyNo", "=", $companyNo);
            });

        //put the conditions and fetched only share linesheet of loggind User
        if ($union) {
            $lineSheets = $lineSheets->where("lss.ShareTo", "=", $loggedInUser);
        }

        if (!$union) {

            $lineSheets = $lineSheets->Where(function ($query) use ($loggedInUser, $brands, $shareLinesheets, $specificUser) {
                $query->where(function ($query) use ($brands, $loggedInUser, $shareLinesheets) {
                    $query->where("LineSheets.visibility", "=", 1)
                        ->where("LineSheets.CreatedBy", "=", $loggedInUser)
                        ->whereIn("LineSheets.brand", $brands);
                })
                    ->orWhere(function ($query) use ($brands, $shareLinesheets, $specificUser) {
                        $query->Where("LineSheets.visibility", "=", 0)
                            ->whereIn("LineSheets.brand", $brands);
                        if ($specificUser) {
                            $query->Where("LineSheets.CreatedBy", "=", Auth::id());
                        }
                        $query->whereNotIn("LineSheets.id", $shareLinesheets);
                    });
            });
        }

        $lineSheets = $lineSheets->where("LineSheets.isArchived", $isArchived);
        // Apply the where filters
        if (isset($filters['Filter']) && count($filters) > 0) {

            foreach ($filters['Filter'] as $key => $value) {

                $lineSheets = Helpers::addDynamicWheres($lineSheets, $value);
            }
        }

        /**  Apply the input search */
        if (isset($filters['Search']) && count($filters['Search']) > 0) {

            $lineSheets = Helpers::addDynamicSearch($lineSheets, $filters['Search']);
        }

        return $lineSheets;
    }

    public static function getUserLineSheets($userId)
    {
        return LineSheets::select(DB::raw("id as ID"), DB::raw("Replace(TRIM(lineSheetName),'','') as value"))->where('createdBy', $userId)->get();
    }

    public static function getLinesheet($linesheetId)
    {
        try {
            $linesheet = LineSheets::find($linesheetId);
            // $linesheet->groups = LineSheets::find($linesheetId)->groups()->select('id as ID', 'GroupName as value')->get();
            $linesheet->groups =  LineSheetGroup::getLineSheetGroups($linesheetId);
            $linesheet->createdById = $linesheet->createdBy;
            $linesheet->createdBy = User::select('name')->where('id', $linesheet->createdBy)->get()[0]->name;
            $linesheet->visibility == 0 ? $linesheet->lsVisibility = 'Public' : $linesheet->lsVisibility = 'Private';
            if (LineSheetShare::where('ShareTo', Auth::id())->where('LineSheetId', $linesheetId)->exists()) {
                $linesheet->lsVisibility = "Shared";
            }
            return $linesheet;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function updatePath($bannerPath, $linesheetId)
    {
        LineSheets::where("id", $linesheetId)->update(['bannerPath' => $bannerPath]);
    }

    //access for linesheet edit
    public static function canLineSheetEdit($linesheetId)
    {

        try {
            if (
                LineSheets::where('createdBy', Auth::id())->where('id', $linesheetId)->count() > 0
                ||
                LineSheetShare::where('ShareTo', Auth::id())->where('LineSheetId', $linesheetId)->count() > 0
            ) {
                return 1;
            } elseif (LineSheets::where('visibility', 0)->where('LineSheetId', $linesheetId)->where('createdBy', '!=', Auth::id())->count() > 0) {
                return 0;
            } else {
                return 0;
            }
        } catch (\Throwable $th) {
            return 0;
        }
    }
}
