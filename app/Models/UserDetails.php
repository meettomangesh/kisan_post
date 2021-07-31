<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

class UserDetails extends Model
{
    //use SoftDeletes;

    public $table = 'user_details';

    protected $dates = [
        'created_at',
        'updated_at',
        // 'deleted_at',
    ];

    protected $fillable = [

        'user_id',
        'role_id',
        'user_photo',
        'aadhar_card_photo',
        'pan_card_photo',
        'license_card_photo',
        'rc_book_photo',
        'bank_name',
        'account_number',
        'ifsc_code',
        'aadhar_number',
        'pan_number',
        'license_number',
        'vehicle_type',
        'vehicle_number',
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
}
