<?php

namespace App\Models;

use App\Http\Middleware\Authenticate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request as Request;
use App\Models\WorkspaceColors;
use App\Models\COLRMS0;
use App\Models\Product;
use App\User;
use App\Utilities\Helpers;
use Illuminate\Support\Facades\Auth;

class Workspaces extends BaseModel
{
    public $table = 'Workspaces';

    public $timestamps = false;

    protected $guarded = [];

    public function colors()
    {
        return $this->belongsToMany('App\Models\COLRMS0', 'WorkspaceColors', 'WorkspaceId', 'ColorId');
    }

    public static function upsertWorkspaces($requestWorkspaces)
    {
        try {
            foreach ($requestWorkspaces as $r) {
                $ws = Workspaces::select('Id')->where('UserId', Auth::id())->where('ProductId', $r['ProductId'])
                    ->where('CompanyNo', Auth::user()->CompanyNo)->where('Type', $r['Type'])->exists();
                if (!$ws) {
                    Workspaces::create([
                        'UserId' => Auth::id(),
                        'Type' => $r['Type'],
                        'ProductId' => $r['ProductId'],
                        'CompanyNo' => Auth::user()->CompanyNo,
                    ]);
                    $id = DB::getPdo()->lastInsertId();
                    $workspace = new Workspaces;
                    $workspace->Id = $id;
                    $arr = [];
                    foreach ($r['ColorId'] as $color) {
                        $arr[] = array("WorkspaceId" => $id, "ColorId" => $color, "CompanyNo" => Auth::user()->CompanyNo);
                    }
                    $workspace->colors()->sync($arr);
                } else {
                    $wid = Workspaces::where('UserId', Auth::id())->where('ProductId', $r['ProductId'])->where('CompanyNo', Auth::user()->CompanyNo)
                        ->where('Type', $r['Type'])->pluck('Id');
                    WorkspaceColors::deleteWorkspace($wid[0]);
                    foreach ($r['ColorId'] as $color) {
                        WorkspaceColors::insert([
                            'WorkspaceId' => $wid[0],
                            'ColorId' => $color,
                            'CompanyNo' => Auth::user()->CompanyNo,
                        ]);
                    }
                }
            }
        } catch (\Throwable $th) {
            $arr = ['message' => "Something went wrong", 'error' => $th->getMessage()];
            return $arr;
        }
    }

    public static function deleteWorkspaceProduct($id)
    {
        try {
            Workspaces::where('Id', '=', $id)->delete();
        } catch (\Throwable $th) {
            $arr = ['message' => "Something went wrong", 'error' => $th->getMessage()];
            return $arr;
        }
    }

    public static function showAll($Type)
    {
        try {
            if (Workspaces::where("Type", $Type)->where("userId", Auth::id())->where('CompanyNo', Auth::user()->CompanyNo)->exists()) {
                $workspaceID = Workspaces::select('Id as ID', 'productId as Product')
                    ->where('CompanyNo', Auth::user()->CompanyNo)
                    ->where("Type", $Type)->where("userId", Auth::id())->get();
                $userId =  Auth::id();
                $companyId = Auth::user()->CompanyNo;
                $divisions = [];
                //get User Divisions
                foreach (User::find(Auth::id())->Divisions->pluck('DivisionNo') as $d) {
                    $divisions[] = $d;
                };
                $filters = array();
                $filters['SortBy'] = [];
                $filters['Search'] = [];
                $filters['Filter'] = array();
                $filters['pageNumber'] = 0;
                $filters['recordPerPage'] = 1;

                foreach ($workspaceID as $w) {
                    $filters['productId'] = $w['Product'];
                    $colors = WorkspaceColors::select('ColorId')->where('WorkspaceId', "=", $w['ID'])->get()->map(function ($item) {
                        return $item['ColorId'];
                    });
                    $Products = Product::getProducts($userId, $companyId, $filters, $divisions);
                    $workspace[$w['ID']] = Helpers::getProductJson($Products, $colors);
                }
                $var = array_values($workspace);
                return response()->json($var);
            }
        } catch (\Throwable $th) {
            $arr = ['message' => "Something went wrong", 'error' => $th->getMessage()];
            return $arr;
        }
    }

}
