<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LSGPPrivatNotes extends BaseModel
{
    /**
     * The table use against this model
     *
     * @var string
     * Table Name : LSGPPrivatNotes
     */
    public $table = 'LSGPPrivatNotes';
    /**
     * The Primary key used withing table
     *
     * @var string
     */
    public $primaryKey = 'id';

    /**
     * Primary key is incremented
     *
     * @var boolean
     */
    public $incrementing = true;

    /**
     * Attributes need to be filled
     */
    protected $fillable = ['CompanyNo', 'LSGPId', 'UserId', 'ProductId', 'Notes'];




    /** Relationships */

    public function lsgproduct()
    {
        return $this->belongsTo('App\Models\LineSheetGroupProducts', 'LSGPId', 'id');
    }


    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'ProductId', 'Style');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'UserId', 'Id');
    }


    // CRUD

    public static function addPrivateNotes($lsgpId = 0, $notes = "", $productId = "")
    {

        try {

            DB::beginTransaction();
            $created =  LSGPPrivatNotes::create([
                "LSGPId" => $lsgpId,
                "Notes" => $notes,
                "UserId" => Auth::id(),
                "ProductId" => $productId
            ]);
            DB::commit();

            return $created;
        } catch (\Throwable $th) {
            //DB::rollback();
            return $th->getMessage();
        }
    }

    public static function updatePrivateNotes($lsgppId = 0, $notes = "")
    {
        try {
            DB::beginTransaction();
            $LsgppRecord = LSGPPrivatNotes::where("id", $lsgppId)
                ->update([
                    "Notes" => $notes
                ]);
            DB::commit();
            return $LsgppRecord;
        } catch (\Throwable $th) {
            Db::rollBack();
            return $th->getMessage();
        }
    }

    public static function deletePrivateNotes($lsgppId = 0)
    {
        try {
            DB::beginTransaction();
            $LsgppRecord = LSGPPrivatNotes::where("id", $lsgppId)->delete();
            DB::commit();
            return $LsgppRecord;
        } catch (\Throwable $th) {
            Db::rollBack();
            return $th->getMessage();
        }
    }
}
