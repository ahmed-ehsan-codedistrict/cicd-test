<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LinesheetHdr extends Model
{
    public $table = 'LinesheetHdr';

    protected $fillable = ['SEQKEY','CompanyNo','CustNo','Customer','Region','ListName','HDateCreated','HUserCreated','HDateMaintained','HUserMaintained','SortOrder','SortList','SortCriteria'];

    public function COMPMS0()
    {
        return $this->belongsTo('App\Models\COMPMS0');
    }
}