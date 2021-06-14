<?php



namespace  App\Models;

use App\Models\BaseModel;
use App\Models\ORTPMS0;
use App\Utilities\Helpers;
use App\Models\EcommCustomer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CUSTMS0 extends BaseModel
{
    public $table = 'CUSTMS0';

    protected $fillable = ['CompanyNo', 'CustomerNo', 'FLNM2S', 'SHNM2S', 'EXNM2S', 'DBNM2S', 'CLCD2S', 'SCCD2S', 'RGCD2S', 'TRCD2S', 'CPCD2S', 'STCD2S'];

    public function ecomm()
    {
        return $this->hasOne(EcommCustomer::class , 'Custno','CustomerNo');
    }

    public function region()
    {
        return $this->hasMany(CSSRMS0::class , 'CustomerNo','CustomerNo');
    }

    public static function getCustomerListing()
    {
        try {
            $customers['Orders'] = ORTPMS0::all(['TPDS3Q','Ecommerce']);
            $customers['Customers'] = CUSTMS0::with(['ecomm','region'])->get();
            return $customers; 
        } catch (\Throwable $th) {
            $err = ['message' => "Something went wrong", 'error' => $th->getMessage()];
            return $err;
        }
    }

    public function COMPMS0()
    {
        return $this->belongsTo('App\Models\COMPMS0');
    }
}
