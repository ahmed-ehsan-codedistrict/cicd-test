<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LineSheetShare extends BaseModel
{
    /**
     * The table use against this model
     *
     * @var string
     * Table Name : LineSheets
     */
    public $table = 'LineSheetShare';
    /**
     * The Primary key used withing table
     *
     * @var string
     */
    public $primaryKey = 'Id';

    /**
     * Primary key is not incremented
     *
     * @var boolean
     */
    public $incrementing = true;


    /**
     * Attributes need to be filled
     */
    protected $fillable = ['CompanyNo', 'CompanyNo', 'ShareBy', 'ShareTo', 'LineSheetId', 'UpdatedBy'];

    public function user()
    {
        return $this->belongsTo('App\User', 'ShareTo', 'id');
    }
    //bulk insert of linesheet share
    public static function bulkLineSheetShare($records)
    {
        try {

            DB::beginTransaction();
            $records =  LineSheetShare::insert($records);
            DB::commit();

            return $records;
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
}
