<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomNotification extends Model
{
     /**
     * The table use against this model
     *
     * @var string
     * Table Name : CustomNotification
     */
    public $table = 'CustomNotifications';
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
        'CompanyNo', 'UserId', 'Message', 'IsRead', 'IsDeleted'
    ];

}
