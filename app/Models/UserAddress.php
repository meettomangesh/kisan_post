<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

class UserAddress extends Model
{
    //use SoftDeletes;

    public $table = 'user_address';

    protected $dates = [
        'created_at',
        'updated_at',
       // 'deleted_at',
    ];

    protected $fillable = [
        'name',
        'address',
        'landmark',
        'pin_code',
        'area',
        'city_id',
        'state_id',
        'mobile_number',
        'user_id',
        'status',
        'created_at',
        'updated_at',
       // 'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function city()
    {
        return $this->belongsTo('App\City');
    }

    public function state()
    {
        return $this->belongsTo('App\State');
    }

    public function pincode()
    {
        return $this->belongsToMany(PinCode::class);
    }
}
