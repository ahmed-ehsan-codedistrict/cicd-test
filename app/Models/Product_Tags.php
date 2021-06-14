<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product_Tags extends Model
{

    /**
     * The table use against this model
     *
     * @var string
     * Table Name : Product_Tags
     */
    public $table = 'Product_Tags';
    /**
     * The Primary key used withing table
     *
     * @var string
     */
    public $primaryKey = 'id';

    /**
     * Primary key is  incremented
     *
     * @var boolean
     */
    public $incrementing = true;


    /**
     * Automatically insert time in data
     * @var boolean
     */
    public $timestamps = true;


     /**
      * Attributes need to be filled
      */
    protected $fillable = ['companyNo','ProductId','TagId','userId'];
}
