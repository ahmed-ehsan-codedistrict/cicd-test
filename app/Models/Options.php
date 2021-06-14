<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Options extends BaseModel
{
    /**
     * The table use against this model
     *
     * @var string
     * Table Name : Options
     */
    public $table = 'Options';
    /**
     * The Primary key used withing table
     *
     * @var string
     */
    public $primaryKey = 'id';

    /**
     * Primary key is not incremented
     *
     * @var boolean
     */
    public $incrementing = true;


    /**
     * Attributes need to be filled
     */
    protected $fillable = [
        'CompanyNo', 'DisplayID', 'DisplayValue', 'TableName', 'TableColumn'
    ];
}
