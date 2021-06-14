<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Tags extends BaseModel
{
    //protected $dateFormat = 'Y-m-d H:i:s';
   /**
     * The table use against this model
     *
     * @var string
     * Table Name : Tags
     */
    public $table = 'Tags';
    /**
     * The Primary key used withing table
     *
     * @var string
     */
    public $primaryKey = 'TagId';

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
    protected $fillable = ['companyNo','TagName'];


    /** Relationships */

    /** one product has many tags */
    public function Products()
    {
        return $this->belongsToMany('App\Models\Product','Product_Tags','TagId','ProductId');
    }

    public function Orders()
    {
        return $this->belongsToMany('App\Models\Product','PreOrderHdr_Tags','TagId','PreOrderNum');
    }

    /** one user has many tags */
    public function Users()
    {
        return $this->belongsToMany('App\User','Product_Tags','TagId','userId');
    }

    public static function getTagsIdNameByIds($tagIds) {

        return Tags::whereIn('TagId', $tagIds)->get(['TagId as id', 'TagName as name']);
    }
}
