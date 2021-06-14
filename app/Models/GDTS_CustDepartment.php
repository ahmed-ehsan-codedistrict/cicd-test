<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GDTS_CustDepartment extends Model
{
    protected $table = 'GDTS_CustDepartment';

    protected $fillable = ['Companyno','Custno','Department'];
}
