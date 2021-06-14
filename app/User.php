<?php

namespace App;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Scopes\TenantScope;
use App\Models\LineSheetShare;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    // use \Eloquence\Behaviours\CamelCasing;
    use Notifiable;
    use SoftDeletes;

    public $table = 'users';

    public $companyColumn = "CompanyNo";

    public $primaryKey = "id";

    //protected $dateFormat = 'Y-m-d H:i:s';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    //    protected $fillable = [
    //        'name', 'email', 'password',
    //    ];

    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'forget_token', 'forget_token_expires_at', 'deleted_at', 'created_at', 'updated_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];



    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new TenantScope);
    }

    public function save(array $options = [])
    {
        if (!empty(request()->header('CompanyNo'))) {
            $this->attributes['CompanyNo'] = intval(request()->header('CompanyNo'));
        }
        parent::save($options);
    }

    /**
     * one user has many tags
     *
     * */

    public function Tags()
    {
        return $this->belongsToMany('App\Models\Tags', 'Product_Tags', 'userId', 'TagId');
    }

    /**
     * one user has many LineSheets
     */
    public function LineSheets()
    {
        return $this->hasMany('App\Models\LineSheets', 'createdBy', 'id')->withoutGlobalScope('unsubscribed');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\COMPMS0', 'CompanyNo', 'CompanyNo');
    }

    public function Divisions()
    {
        return $this->belongsToMany('App\Models\DIVNMS0', 'DivisionUser', 'UserId', 'DivisionNo');
    }

    public function Brands()
    {
        return $this->belongsToMany('App\Models\ProdPLM', 'UserBrand', 'UserId', 'Brand');
    }

    public function lsgprivatenotes()
    {
        return $this->hasMany('App\Models\LSGPPrivatNotes', 'UserId', 'id');
    }

    public function salesRep()
    {
        return $this->hasOne('App\Models\SLMNMS0');
    }

    public function uBrands()
    {
        return $this->hasMany('App\Models\UserBrand', 'UserId', 'id');
    }
    public function shareLinesheet()
    {
        return $this->hasMany('App\Models\LineSheetShare', 'ShareTo', 'id');
    }
    public static function getSalesRep($linesheetId)
    {
        $companyNo = Auth::user()->CompanyNo;
        $salesRep = DB::table("Users as us")
            ->select(
                "us.id as ID",
                DB::raw("Replace(TRIM(sp.FLNM2H),'','') as value"),
                "us.parent_id as ManagerID"
            );  //->whereNotIn('us.id', [Auth::id()]);


        $salesRep = $salesRep->Join('SLMNMS0 as sp', function ($join) use ($companyNo) {
            $join->on('sp.UserId', '=', 'us.id')
                ->where('sp.CompanyNo', '=', $companyNo);
        })->orderBy('value')->get();
        foreach ($salesRep as $s) {
            LineSheetShare::where('ShareTo', $s->ID)->where('LineSheetId', $linesheetId)->exists() ? $s->shared = true : $s->shared = false;
        }
        return $salesRep;
    }

    // user Divisions
    public static function userDivisions()
    {
        $divisions = [];

        //get User Divisions
        foreach (User::find(Auth::id())->Divisions->pluck('DivisionNo') as $d) {
            $divisions[] = $d;
        };
        return $divisions;
    }

    //user Brand
    public static function userBrands()
    {
        $brands = [];

        //get User Divisions
        foreach (User::find(Auth::id())->uBrands->pluck("Brand") as $b) {
            $brands[] = trim($b);
        };
        return $brands;
    }
    //shareLineshet
    public static function userShareLinesheet()
    {
        $LineSheet = [];

        //get User Divisions
        foreach (User::find(Auth::id())->shareLinesheet->pluck("LineSheetId") as $b) {
            $LineSheet[] = trim($b);
        };
        return $LineSheet;
    }
}
