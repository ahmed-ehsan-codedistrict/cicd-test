<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CustomSort extends BaseModel
{
    /**
     * The table use against this model
     *
     * @var string
     * Table Name : CustomNotification
     */
    public $table = 'CustomSort';
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
        'CompanyNo', 'CombinationId', 'LSGPId', 'DisplayOrder'
    ];

    public static function bulkUpdate($ids, $cases, $CompanyNo, $groupId, $params)
    {
        if (!empty($ids)) {
            return DB::update("UPDATE CustomSort SET DisplayOrder = CASE id {$cases} END
               WHERE id in ({$ids})
               AND CompanyNo ={$CompanyNo}", $params);
        }
        return false;
    }

    public static function getRecordsByCombinationId($CombinationId)
    {
        try {
            $CustomSortRecords =   CustomSort::select('id', 'LSGPId', 'CombinationId', 'DisplayOrder')
                ->where('CombinationId', $CombinationId)
                ->orderBy('DisplayOrder', 'asc')
                ->get();
            return $CustomSortRecords;
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public static function updateDisplayOrder($CustomSortArr)
    {

        $query = '';
        $ids = [];
        $cases = [];
        $params = [];
        $disOrder  = 1;
        foreach ($CustomSortArr as $ph) {
            $ids[] = $ph->id;
            $cases[] = "WHEN {$ph->id} then ?";
            $params[] = $disOrder;
            $disOrder++;
        }
        $ids = implode(',', $ids);
        $cases = implode(' ', $cases);
        //raw query to update the DIsplay order
        return  CustomSort::bulkUpdate($ids, $cases, Auth::user()->CompanyNo, 0, $params);
    }
}
