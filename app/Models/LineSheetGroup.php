<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class LineSheetGroup extends BaseModel
{

    /**
     * The table use against this model
     *
     * @var string
     * Table Name : LineSheetGroup
     */
    public $table = 'LineSheetGroup';
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
    protected $fillable = ['CompanyNo', 'GroupName', 'LineSheetId', 'CreatedBy'];



    /** Relationships */

    public function linesheet()
    {
        return $this->belongsTo(LineSheets::class, 'id', 'LineSheetId');
    }

    public function products()
    {
        return $this->belongsToMany('App\Models\Product', 'LineSheetGroupProducts', 'GroupId', 'ProductId');
    }


    /** CRUD */

    public static function createGroup($groupName = "", $lineSheetId = 0)
    {
        try {
            DB::beginTransaction();
            $IsCreate  = LineSheetGroup::create([
                // "CompanyNo" => 1,
                "CreatedBy" => Auth::id(),
                "GroupName" => $groupName,
                "LineSheetId" => $lineSheetId
            ]);

            DB::commit();

            return $IsCreate;
        } catch (\Throwable $th) {
            DB::rollback();
            return  $th;
        }
    }



    /**
     * update the LineSheet Group
     */

    public static function updateGroup($id = 0, $groupName = "", $lineSheetId = 0)
    {
        try {

            DB::beginTransaction();

            $LineSheetsGroup  = LineSheetGroup::find($id);
            $LineSheetsGroup->GroupName =  $groupName;
            $LineSheetsGroup->LineSheetId =  $lineSheetId;
            $isUpdated =  $LineSheetsGroup->save();

            DB::commit();

            return true;
        } catch (\Throwable $th) {
            DB::rollback();
            return  $th;
        }
    }

    public static function getLineSheetGroups($linesheetId = 0, $groupId = 0)
    {
        $companyNo =  Auth::user()->CompanyNo;
        $LineSheetGroup =  LineSheetGroup::distinct("ID")->select(
            DB::raw("LineSheetGroup.id as ID"),
            DB::raw("Replace(TRIM(LineSheetGroup.GroupName),'','') as value"),
            "ls.createdBy as lsCreatedBy",
            "LineSheetGroup.CreatedBy as groupCreatedBy",
            DB::raw("  case
                    when cc.id > 0 then 'true'
                    else
                     'false'
                end as customSort
            ")
        )
            ->Join("LineSheets as ls", function ($join) use ($companyNo) {
                $join->on("ls.id", "=", "LineSheetGroup.LineSheetId");
                $join->where("ls.CompanyNo", "=", $companyNo);
            })
            ->leftJoin("CustomSortCombination as cc", function ($join) use ($companyNo) {
                $join->on("cc.GroupId", "=", "LineSheetGroup.id");
                $join->on("cc.LineSheetId", "=", "ls.id");
                $join->where("cc.CompanyNo", "=", $companyNo);
                $join->where("cc.UserId", "=", Auth::id());
            });
        if ($groupId > 0) {
            $LineSheetGroup =  $LineSheetGroup->where("LineSheetGroup.id", $groupId);
        }
        if ($linesheetId > 0) {
            $LineSheetGroup =  $LineSheetGroup->where('LineSheetGroup.LineSheetId', $linesheetId);
        }
        return $LineSheetGroup->get();
    }

    public static function deleteLinesheetGroup($groupId)
    {
        LineSheetGroup::where('id', $groupId)->delete();
    }
    public static function duplicateGroup($groupId, $lineSheetId)
    {
        try {

            DB::beginTransaction();

            $group = LineSheetGroup::find($groupId);

            $newGroup = $group->replicate();
            $newGroup->CompanyNo  = Auth::user()->Company;
            $newGroup->LineSheetId  = $lineSheetId;
            $newGroup->CreatedBy  = Auth::id();
            $newGroup->created_at  = Carbon::now();
            $newGroup->updated_at  = Carbon::now();

            $response =  $newGroup->save();

            DB::commit();

            return $newGroup->id;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th->getMessage();
        }
    }
}
