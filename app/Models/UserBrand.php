<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserBrand extends BaseModel
{
    public $table = 'UserBrand';

    protected $guarded = [];


    public function Users()
    {
        return $this->belongsTo('App\User','UserId','id');
    }
    public static function getUserBrands()
    {
        return UserBrand::select(DB::raw('trim(Brand) as ID'),DB::raw('trim(Brand) as value'))->where('UserId', Auth::id())->get();
    }
}
