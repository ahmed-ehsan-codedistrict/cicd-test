<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GDTS_Labels extends Model
{
    protected $table = 'GDTS_Labels';

    protected $fillable = ['labels','CompanyNo','DateCreated','UserCreated','DateMaintained','UserMaintained'];
}

