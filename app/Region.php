<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

class Region extends Model
{
    //use SoftDeletes;

    public $table = 'region_master';

    protected $dates = [
        'created_at',
        'updated_at',
      //  'deleted_at',
    ];

    protected $fillable = [
        'region_name',
        'status',
        'created_at',
        'updated_at',
       // 'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    

    public function pin_codes()
    {
        return $this->belongsToMany(PinCode::class);
    }

    public static function boot() {
        parent::boot();

        static::deleting(function($region) {
             $region->pin_codes()->get()->each->delete();
        });
    }

    // public function country()
    // {
    //     return $this->belongsTo(Country::class, 'country_id');
    // }

    // public function state()
    // {
    //     return $this->belongsTo(State::class, 'state_id');
    // }

    // public function city()
    // {
    //     return $this->belongsTo(City::class, 'city_id');
    // }
}
