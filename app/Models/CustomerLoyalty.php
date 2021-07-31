<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

class CustomerLoyalty  extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'customer_loyalty';

    //

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','first_name','last_name', 'mobile_number', 'email_address', 'password', 'email_verify_key', 'referral_code', 'created_by'
    ];

    public function getAuthPassword()
    {
        return $this->password;
    }
}
