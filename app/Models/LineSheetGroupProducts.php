<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PRDTMS0;
use App\Models\LSGPPrivatNotes;
use Carbon\Carbon;
use Faker\Provider\Base;
use App\Models\CustomSort;

class LineSheetGroupProducts extends BaseModel
{
    /**
     * The table use against this model
     *
     * @var string
     * Table Name : LineSheetGroupProducts
     */
    public $table = 'LineSheetGroupProducts';
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
    protected $fillable = ['GroupId', 'ProductId', 'CreatedBy', 'UpdatedBy', 'ColorId', 'PublicNotes', 'PublicNotesCreatedBy', 'DisplayOrder', 'LinesheetId'];


    /** Relationships */

    public function lsgprivatenotes()
    {
        return $this->hasMany('App\Models\LSGPPrivatNotes', 'LSGPId', 'id');
    }



    /** CRUD */

    public static function bulkAddProducts($records)
    {


        try {

            DB::beginTransaction();
            $created = LineSheetGroupProducts::insert($records);
            DB::commit();

            return $created;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th->getMessage();
        }
    }

    public static function addProductsToGroup($groupId = 0, $productId = 0, $colorId = null, $DisplayOrder = 1)
    {

        try {

            if (!LineSheetGroupProducts::getLastDisplayOrder($groupId)) {
                $DisplayOrder = LineSheetGroupProducts::getLastDisplayOrder($groupId)[0]['DisplayOrder'] + 1;
            }
            DB::beginTransaction();
            if (PRDTMS0::where('Color', $colorId)->where('Style', $productId)->exists()) {
                $created =   LineSheetGroupProducts::create([
                    'GroupId' => $groupId,
                    'ProductId' => $productId,
                    'ColorId' => $colorId,
                    // 'DisplayOrder' => $DisplayOrder,
                    'CreatedBy' => Auth::id(),
                ]);
            }
            DB::commit();

            return $created;
        } catch (\Throwable $th) {
            DB::rollback();
            return $th->getMessage();
        }
    }

    /**
     * product is already assigned or not;
     * @var string
     */

    public static function isProductAlreadyAssigned($groupId = 0, $productId = 0, $colorId = 0)
    {
        return LineSheetGroupProducts::select(
            "ProductId",
            "ColorId",
            DB::raw("count(*) as NumberOfRecords")
        )
            ->where("ProductId", "=", $productId)
            ->where("GroupId", "=", $groupId)
            ->where("ColorId", "=", $colorId)
            ->groupBy("ProductId")
            ->groupBy("ColorId")
            ->groupBy("GroupId")
            ->get();
    }

    public static function productAlreadyInGroup($LinesheetId = 0, $productId = 0, $colorId = 0)
    {
        $NumberOfProducst = LineSheetGroupProducts::where(
            [
                "LinesheetId" => $LinesheetId,
                "ProductId" => $productId,
                "ColorId" => $colorId
            ]
        )->count();
        return $NumberOfProducst;
    }

    /** add public notes to a product in linesheet */

    public static function addPublicNotes($lsgpId = 0, $publicNotes = "")
    {
        try {
            DB::beginTransaction();
            $LsgpRecord = LineSheetGroupProducts::where("id", $lsgpId)
                ->update([
                    "PublicNotes" => $publicNotes,
                    "PublicNotesCreatedBy" => Auth::id()
                ]);
            DB::commit();
            return $LsgpRecord;
        } catch (\Throwable $th) {
            Db::rollBack();
            return $th->getMessage();
        }
    }

    /**
     * get Product for drag drop
     */

    // public static function getProductsForDragDrop($sortType = "asc", $min, $max, $groupId)
    // {
    //     try {
    //         return LineSheetGroupProducts::select("id", "ProductId", "DisplayOrder", "ColorId")
    //             ->whereBetween('DisplayOrder', [$min, $max])
    //             // ->orWhere('id', $id)
    //             ->where('GroupId', $groupId)

    //             ->orderBy("DisplayOrder", $sortType)
    //             ->get();
    //     } catch (\Throwable $th) {
    //         return $th->getMessage();
    //     }
    // }

    public static function getProductsForDragDrop($sortType = "asc", $min, $max, $CombinationId)
    {
        try {
            return CustomSort::select("id", "DisplayOrder", "LSGPId")
                ->whereBetween('DisplayOrder', [$min, $max])
                // ->orWhere('id', $id)
                ->where('CombinationId', $CombinationId)

                ->orderBy("DisplayOrder", $sortType)
                ->get();
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }


    //get Display Order for a specific group
    public static function getLastDisplayOrder($groupId)
    {
        try {
            $LastDisplayOrder =   LineSheetGroupProducts::select('DisplayOrder')
                ->where('GroupID', $groupId)
                ->orderBy('DisplayOrder', 'desc')
                ->limit(1)
                ->get();
            return $LastDisplayOrder = isset($LastDisplayOrder[0]['DisplayOrder']) && $LastDisplayOrder[0]['DisplayOrder'] > 0 ? $LastDisplayOrder[0]['DisplayOrder'] : 0;
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    //update the display order
    public static function updateDisplayOrder($productArr, $CompanyNo, $groupId)
    {

        $query = '';
        $ids = [];
        $cases = [];
        $params = [];
        $disOrder  = 1;
        foreach ($productArr as $ph) {
            $ids[] = $ph->customSortId;
            $cases[] = "WHEN {$ph->customSortId} then ?";
            $params[] = $disOrder;
            $disOrder++;
        }
        $ids = implode(',', $ids);
        $cases = implode(' ', $cases);
        //raw query to update the DIsplay order
        return  CustomSort::bulkUpdate($ids, $cases, $CompanyNo, $groupId, $params);
    }

    //insert into customSort table

    public static function addToCustomSort($productArr, $CombinationId, $disOrder = 1)
    {
        $records = [];
        foreach ($productArr as $ph) {
            $records[] = [
                "CompanyNo" => Auth::user()->CompanyNo,
                "CombinationId" => $CombinationId,
                "LSGPId" => $ph->id,
                "DisplayOrder" => $disOrder,
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now()
            ];
            $disOrder++;
        }
        // return $records;
        $record =  CustomSort::insert($records);
        return $record;
    }

    //get product display order
    // public static function getProductDisplayOrder($productId, $groupId, $colorId)
    // {
    //     try {
    //         return LineSheetGroupProducts::select('DisplayOrder')
    //             ->where('GroupId', $groupId)
    //             ->where('ProductId', $productId)
    //             ->where('ColorId', $colorId)
    //             ->orderBy('DisplayOrder', 'desc')
    //             ->limit(1)
    //             ->first();
    //     } catch (\Throwable $th) {
    //         return $th->getMessage();
    //     }
    // }

    public static function getLineSheetGroupProductId($productId, $groupId, $colorId)
    {
        try {
            return LineSheetGroupProducts::select('id')
                ->where('GroupId', $groupId)
                ->where('ProductId', $productId)
                ->where('ColorId', $colorId)
                ->limit(1)
                ->first();
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
    public static function getProductDisplayOrder($lpgsId, $CombinationId)
    {
        try {
            return CustomSort::select('DisplayOrder')
                ->where('CombinationId', $CombinationId)
                ->where('LSGPId', $lpgsId)
                ->orderBy('DisplayOrder', 'desc')
                ->limit(1)
                ->first();
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
    //Bulk update for LineSheetGroup Products

    public static function bulkUpdate($ids, $cases, $CompanyNo, $groupId, $params)
    {
        if (!empty($ids)) {
            return DB::update("UPDATE LineSheetGroupProducts SET DisplayOrder = CASE id {$cases} END
               WHERE id in ({$ids})
               AND CompanyNo ={$CompanyNo}
               AND GroupId = {$groupId} ", $params);
        }
        return false;
    }

    public static function getGroupProducts($groupId)
    {
        if (LineSheetGroupProducts::where('GroupId', $groupId)->exists()) {
            return array_values(LineSheetGroupProducts::select('ProductId', 'ColorId')->where('GroupId', $groupId)->get()->toarray());
        }
    }

    public static function getLinesheetGroupProducts($groupId)
    {
        return LineSheetGroupProducts::where('GroupId', $groupId)->exists() ?
            LineSheetGroupProducts::where('GroupId', $groupId)->get() : null;
    }

    public static function deleteGroupProduct($groupId, $productId, $colorId)
    {
        //->where('CreatedBy', Auth::id()
        $lineSheetProducts = LineSheetGroupProducts::where('GroupId', $groupId)->where('ProductId', $productId)->where('ColorId', $colorId);
        if ($lineSheetProducts->exists()) {
            try {
                $lsgpID = $lineSheetProducts->pluck('id');
                if (LSGPPrivatNotes::where('LSGPId', $lsgpID[0])->exists()) {
                    LSGPPrivatNotes::where('LSGPId', $lsgpID[0])->delete();
                }
                $lineSheetProducts->delete();
                return ['message' => "Deleted Successfully", 'success' => true, 'error' => null];
            } catch (\Throwable $th) {
                return ['message' => "Something went wrong", 'success' => false, 'error' => $th->getMessage()];
            }
        } else {
            return ['message' => "Something went wrong", 'success' => false, 'error' => "No such product exists in this group"];
        }
    }

    public static function deleteGroupProductNotes($groupId, $productId, $colorId)
    {
        $lineSheetProducts = LineSheetGroupProducts::where('GroupId', $groupId)->where('ProductId', $productId)->where('ColorId', $colorId)->where('CreatedBy', Auth::id());
        if ($lineSheetProducts->exists()) {
            try {
                $lsgpID = $lineSheetProducts->pluck('id');
                if (LSGPPrivatNotes::where('LSGPId', $lsgpID[0])->where('UserId', Auth::id())->exists()) {
                    LSGPPrivatNotes::where('LSGPId', $lsgpID[0])->where('UserId', Auth::id())->delete();
                    return ['message' => "Deleted Successfully", 'success' => true, 'error' => null];
                } else {
                    return ['message' => "Notes doesn't exists", 'success' => false, 'error' => "User Notes for this product doesn't exists"];
                }
            } catch (\Throwable $th) {
                return ['message' => "Something went wrong", 'success' => false, 'error' => $th->getMessage()];
            }
        } else {
            return ['message' => "Something went wrong", 'success' => false, 'error' => "No such product exists in this group"];
        }
    }

    //count the values which has display values null
    // public static function getNullValues($groupId)
    // {
    //     try {
    //         return  LineSheetGroupProducts::where("GroupId", $groupId)
    //             ->where(function ($query) {
    //                 $query->whereNull('DisplayOrder')
    //                     ->orWhere('DisplayOrder', '');
    //             })
    //             ->count();
    //     } catch (\Throwable $th) {
    //         return  1;
    //     }
    // }

    public static function getNullValues($CombinationId)
    {
        try {
            return  CustomSort::where("CombinationId", $CombinationId)
                ->where(function ($query) {
                    $query->whereNull('DisplayOrder')
                        ->orWhere('DisplayOrder', '');
                })
                ->count();
        } catch (\Throwable $th) {
            return  1;
        }
    }
}
