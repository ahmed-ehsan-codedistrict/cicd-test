<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\TenantScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Builder as Builder;
use Illuminate\Database\Connection as Connection;
use App\Utilities\Helpers;
use App;

class BaseModel extends Model
{

    // use \Eloquence\Behaviours\CamelCasing;

    protected $dateFormat = 'Y-m-d H:i:s';

    public function getDates()
    {
        return [];
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new TenantScope);
    }

    public function save(array $options = [])
    {
        if (!App::runningInConsole()) {
            $this->attributes['CompanyNo'] = intval(request()->header('CompanyNo'));
        }
        parent::save($options);
    }

    public static function getTableColumnAll($tableName, $idColumn, $valueColumn, $like = null, $foreignKey = null, $selectedValue = null, $userId = null, $idNotIn = null, $options = false)
    {

        try {
            if ($options) {
                $OptionTableColumn = $valueColumn;
                $idColumn = 'DisplayID';
                $valueColumn = 'DisplayValue';
                //OptionTableName is used in where condition when search on options
                $OptionTableName = $tableName;
                $tableName = "Options";
            }

            $valueType = DB::connection()->getDoctrineColumn($tableName, $valueColumn)->getType()->getName();
            $idColumn = (empty($idColumn)) ? $valueColumn : $idColumn;
            $modelName = "App\Models\\" . $tableName;
            $idType = DB::connection()->getDoctrineColumn($tableName, $idColumn)->getType()->getName();
            $OrderByColumn = $valueColumn;

            if ($valueColumn == "Season") {
                $OrderByColumn = "SUBSTRING(Season, LEN(Season)-4,5)";
            }

            if ($valueColumn == "FabType") {
                $OrderByColumn = "CASE
                  WHEN PATINDEX('%[0-9,),:,(,*,&,^,$]%',SUBSTRING(ProdPLM.FabType, 1, 1)) > 0 THEN 2
                  ELSE   1
               END ";
            }

            if ($valueColumn == "CDES3J") {
                $OrderByColumn = "case
                  when left(CDES3J,1) like '%[A-Z]%' then  left(CDES3J,1)
                  else concat('z', left(CDES3J,1))
               end ";
            }



            if ($valueColumn == "MKDS3N") {
                $OrderByColumn = "MKDS3N";
            }

            if ($valueColumn == "DVNM3C") {
                $OrderByColumn = "DVNM3C";
            }

            if ($valueColumn == "TagName") {
                $OrderByColumn = "TagName";
            }

            if ($valueColumn == "FLNM2S") {
                $OrderByColumn = "FLNM2S";
            }

            if (
                ($valueType == 'string' || $valueType == 'char')
                &&  ($idType == 'string' || $idType == 'char')
            ) {
                $results = $modelName::distinct($valueColumn)
                    ->select(DB::raw("Replace(TRIM($idColumn),'','') as ID"), DB::raw("Replace(TRIM($valueColumn),'','') as value"),  DB::raw("$OrderByColumn as OrderByColumn"));
            } else if (
                ($valueType == 'int' || $valueType == 'integer' || $valueType == 'bigint' || $valueType == 'decimal' || $valueType == 'double' || $valueType == 'float')
                &&
                ($idType == 'int' || $valueType == 'integer' || $idType == 'bigint' || $idType == 'decimal' || $idType == 'double' || $valueType == 'float')
            ) {
                $results = $modelName::distinct($valueColumn)
                    ->select(DB::raw("$idColumn as ID"), DB::raw("$valueColumn as value"), DB::raw("$OrderByColumn as OrderByColumn"));
            } else if (
                ($valueType == 'string' || $valueType == 'char')
                &&
                ($idType == 'int' || $idType == 'integer' || $idType == 'bigint' || $idType == 'decimal' || $idType == 'double' || $valueType == 'float')
            ) {
                $results = $modelName::distinct($valueColumn)
                    ->select(DB::raw("$idColumn as ID"), DB::raw("Replace(TRIM($valueColumn),'','') as value"), DB::raw("$OrderByColumn as OrderByColumn"));
            } else {

                $results = $modelName::distinct($valueColumn)
                    ->select(DB::raw("Replace(TRIM($idColumn),'','') as ID"), DB::raw("$valueColumn as value"), DB::raw("$OrderByColumn as OrderByColumn"));
            }

            $results =  $results->whereNotNull($valueColumn)->whereRaw("$valueColumn!=''");
            if ($like) {
                $results = $results->where($valueColumn, 'like', '%' . $like . '%');
            }
            if ($foreignKey && $selectedValue) {
                $results = $results->where($foreignKey, "=", $selectedValue);
            }
            if ($idNotIn) {
                $results = $results->whereNotIn($idColumn, $idNotIn);
            }
            if ($tableName == 'DIVNMS0') {
                //filtering divisions on user
                if (!empty($userId)) {
                    $results->whereHas('Users', function ($query) use ($userId) {
                        $query->where('id', $userId);
                    });
                }
            }
            if ($tableName == 'ProdPLM') {

                //filtering brands on user
                if (!empty($userId)) {
                    $results->whereHas('Users', function ($query) use ($userId) {
                        $query->where('id', $userId);
                    });
                }
            }
            if ($valueColumn == 'Season') {
                $results = $results->orderByRaw('SUBSTRING(Season, LEN(Season)-4,5) DESC');
            }
            if ($valueColumn == 'Market') {
                $results = $results->orderByRaw('Market DESC');
            }
            if ($valueColumn == "FabType") {
                $results = $results->orderByRaw("
                 CASE
                    WHEN PATINDEX('%[0-9,),:,(,*,&,^,$]%',SUBSTRING(ProdPLM.FabType, 1, 1)) > 0 THEN 2
                    ELSE   1
                 END ASC
                 ");
            }

            if ($valueColumn == "CDES3J") {
                $results = $results->orderByRaw("case
                  when left(CDES3J,1) like '%[A-Z]%' then  left(CDES3J,1)
                  else concat('z', left(CDES3J,1))
               end asc ");
            }

            if ($valueColumn == "MKDS3N") {
                $results = $results->orderBy("MKDS3N", "asc");
            }
            if ($valueColumn == "DVNM3C") {
                $results = $results->orderBy("DVNM3C", "asc");
            }
            if ($valueColumn == "TagName") {
                $results = $results->orderBy("TagName", "asc");
            }
            if ($valueColumn == "FLNM2S") {
                $results = $results->orderBy("FLNM2S", "asc");
            }
            if ($options) {
                $results = $results->where(
                    [
                        'TableName' => $OptionTableName,
                        'TableColumn' => $OptionTableColumn,
                    ]

                );
            }
            $results = $results->get();
            return $results;
        } catch (\Throwable $e) {
            $err = ['message' => "Something went wrong", 'error' => $e->getMessage()];
            return $err;
        }
    }

    public static function getTableColumnKeyMultiValues($tableName, $idColumn, $valueColumn)
    {
        try {
            $model_name = 'App\Models\\' . $tableName;
            $type = DB::connection()->getDoctrineColumn($tableName, $valueColumn)->getType()->getName();
            if ($type == 'string') {
                $results = $model_name::select(DB::raw("Replace(TRIM($idColumn),'','') as ID"), DB::raw("string_agg(TRIM(CAST($valueColumn AS NVARCHAR(MAX))), ',') WITHIN GROUP (ORDER BY attribval asc) as value"))
                    ->groupBy($idColumn);
            } else {
                $results = $model_name::select(DB::raw("$idColumn as ID"), DB::raw("string_agg($valueColumn, ',') WITHIN GROUP (ORDER BY attribval asc) as value"))
                    ->groupBy($idColumn);
            }
            $results =  $results->whereNotNull($valueColumn)->whereRaw("$valueColumn!=''");
            $results = $results->get()->toArray();
            $resultJson = array_map("App\Utilities\Helpers::convertValueFromCommaSeperatedToArray", $results);
            return $resultJson;
        } catch (\Throwable $e) {
            $err = ['message' => "Something went wrong", 'error' => $e->getMessage()];
            return response()->json($err, 403);
        }
    }

    //get the values base on specific key
    public static function getSpecificColumnValue($tableName, $idColumn, $valueColumn, $whereValue, $whereColumn)
    {
        try {
            $modelName = 'App\Models\\' . $tableName;
            $idType = DB::connection()->getDoctrineColumn($tableName, $idColumn)->getType()->getName();

            if ($idType == 'string' || $idType == 'char') {
                $results = $modelName::distinct($valueColumn)
                    ->select(
                        DB::raw("Replace(TRIM($idColumn),'','') as ID"),
                        DB::raw("Replace(TRIM($valueColumn),'','') as value"),
                        DB::raw("$valueColumn as OrderByColumn")
                    );
            } else {
                $results = $modelName::distinct($valueColumn)
                    ->select(
                        DB::raw("$idColumn as ID"),
                        DB::raw("Replace(TRIM($valueColumn),'','') as value"),
                        DB::raw("$valueColumn as OrderByColumn")
                    );
            }
            $results =  $results->where($whereColumn, $whereValue)
                ->get();
            return $results;
        } catch (\Throwable $th) {
            $err = ['message' => "Something went wrong", 'error' => $th->getMessage()];
            return response()->json($err, 403);
        }
    }
}
