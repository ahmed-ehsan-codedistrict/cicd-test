<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GDTS_BuyerNew extends Model
{
    protected $table = 'GDTS_BuyerNew';

    protected $fillable = ['CompanyNo','Custno','Divno','BuyerName','Buyno','Addr1','Addr2','City','State','Zip','Country','Phone','Fax','Email','Position'];
}

