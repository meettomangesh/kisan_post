<?php

namespace App\Models;
use PDO;
use Illuminate\Database\Eloquent\Model;
use App\Models\ApiModel;
use DB;

class CustomerDeviceTokens extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'customer_device_tokens';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'user_role_id', 'device_token', 'device_id', 'device_type','status'
    ];


}
