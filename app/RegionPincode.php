<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

class RegionPincode extends Model
{
    //use SoftDeletes;

    public $table = 'region_pincode';

    protected $dates = [
        'created_at',
        'updated_at',
       // 'deleted_at',
    ];

    protected $fillable = [
        'region_id',
        'pincode_id',
        'status',
        'created_at',
        'updated_at',
       // 'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    
    // public function region()
    // {
    //     return $this->belongsTo(Region::class, 'region_id');
    // }

    // public function pincode()
    // {
    //     return $this->belongsTo(PinCode::class, 'pincode_id');
    // }

    // public function city()
    // {
    //     return $this->belongsTo(City::class, 'city_id');
    // }

    public function pincode()
    {
        return $this->belongsToMany(PinCode::class);
    }
}
