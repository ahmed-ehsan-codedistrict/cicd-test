<?php



namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class EcommCustomer extends BaseModel
{
    public $table = 'EcommCustomer';

    protected $fillable = ['CompanyNo', 'Custno', 'EcommName'];

    public function Customer()
    {
        return $this->belongsTo(CUSTMS0::class,'CustomerNo', 'Custno');
    }

    public function COMPMS0()
    {
        return $this->belongsTo('App\Models\COMPMS0');
    }
}
