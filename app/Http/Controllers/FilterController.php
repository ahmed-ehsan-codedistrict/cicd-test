<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FilterController extends Controller
{
    public function getTableColumnAll(Request $request)
    {
        //validating inputs
        $request->validate([
            'table' => 'required',
            'valueColumn' => 'required',
            'idColumn' => 'string|nullable',
            'like' => 'string|nullable',
            'foreignKey' => 'string|nullable',
            'selectedValue' => 'string|nullable',
            'idNotIn' => 'array|nullable',
        ]);
        try {
            $userId = Auth::id();

            $userId = Auth::id();

            $model_name = 'App\Models\\' . $request->table;
            return response()->json($model_name::getTableColumnAll(
                $request->table,
                $request->idColumn,
                $request->valueColumn,
                $request->like,
                $request->foreignKey,
                $request->selectedValue,
                $userId,
                $request->idNotIn,
                $request->option
            ));

        } catch (\Error $e) {
            return response()->json(['message' => "Something went wrong", 'error' => $e->getMessage()], 400);
        }
    }

    public function getTableColumnKeyMultiValues(Request $request)
    {

        //validating inputs
        $request->validate([
            'table' => 'required',
            'valueColumn' => 'required',
            'idColumn' => 'required',
            'like' => 'string|nullable'
        ]);
        try {
            $model_name = 'App\Models\\' . $request->table;
            return response()->json($model_name::getTableColumnKeyMultiValues($request->table, $request->idColumn, $request->valueColumn));
        } catch (\Error $e) {
            return response()->json(['message' => "Something went wrong", 'error' => $e->getMessage()], 400);
        }
    }

    public function getOptionsValue(Request $request)
    {
        //validating inputs
        $request->validate([
            'tableColumn' => 'required|string',
            'tableName' => 'required|string'
        ]);
        try {
            return response()->json(DB::table('Options as op')->select(
                "op.DisplayID as ID",
                "op.DisplayValue as value")->where('TableColumn',$request->tableColumn)->where('TableName',$request->tableName)->where('CompanyNo',Auth::user()->CompanyNo)->get());
        } catch (\Error $e) {
            return response()->json(['message' => "Something went wrong", 'error' => $e->getMessage()], 400);
        }
    }

    public function getSpecificColumnValue(Request $request)
    {
        //validating inputs
        $request->validate([
            'table' => 'required',
            'valueColumn' => 'required',
            'idColumn' => 'required',
            'whereColumn' => 'required',
            'whereValue' => 'required'
        ]);
        try {
            $modelName = 'App\Models\\' . $request->table;
            return response()->json($modelName::getSpecificColumnValue(
                 $request->table,
                 $request->idColumn,
                 $request->valueColumn,
                 $request->whereValue,
                 $request->whereColumn
                ));
        } catch (\Error $e) {
            return response()->json(['message' => "Something went wrong", 'error' => $e->getMessage()], 400);
        }
    }
}
