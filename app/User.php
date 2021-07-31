<?php

namespace App;

use Carbon\Carbon;
use Hash;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use \DateTimeInterface;
use DB;
use PDO;
use App\Helper\DataHelper;
use App\Models\CustomerDeviceTokens;

class User extends Authenticatable
{
    use SoftDeletes, Notifiable, HasApiTokens;

    public $table = 'users';

    protected $hidden = [
        'remember_token',
        'password',
    ];

    protected $dates = [
        'email_verified_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'email_verify_key',
        'email_verified_at',
        'mobile_number',
        'mobile_number_verified_at',
        'status',
        'password',
        'password_plain',
        'remember_token',
        'referral_code',
        'referred_by_user_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'gender',
        'marital_status',
        'date_of_birth',

    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getIsAdminAttribute()
    {
        return $this->roles()->where('id', 1)->exists();
    }

    public function getEmailVerifiedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    public function setEmailVerifiedAtAttribute($value)
    {
        $this->attributes['email_verified_at'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function setEmailVerifyKeyAtAttribute($value)
    {
        $this->attributes['email_verify_key'] = DataHelper::emailVerifyKey();
    }

    public function getEmailVerifyKeyAtAttribute($value)
    {
        return $value ? $value : DataHelper::emailVerifyKey();
    }

    public function setPasswordAttribute($input)
    {
        if ($input) {
            $this->attributes['password'] = app('hash')->needsRehash($input) ? Hash::make($input) : $input;
        }
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function regions()
    {
        return $this->belongsToMany(Region::class);
    }

    public function address()
    {
        return $this->hasMany('App\Models\UserAddress');
    }

    public function details()
    {
        return $this->hasOne('App\Models\UserDetails');
    }


    /**
     * Get pin code details
     * @param array $params
     * @throws Exception  
     * @return array of data
     */
    public function getPinCodeDetails($params = [])
    {
        try {

            $stmt = DB::connection()->getPdo()->prepare("CALL searchPincode(?)");
            $stmt->execute(array($params['pin_code']));
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // print_r($result); exit;
            if ($result) {
                return $result;
            } else {
                return [];
            }
        } catch (Exception $e) {
            //  throw new Exception($e->getMessage());
            return $this->sendError('Error.', $e->getMessage());
        }
    }


    /**
     * check email verified or not
     * return success response or error response in json 
     * return id in data params
     */
    public function checkEmailVerified($customerId)
    {
        try {
            $stmt = DB::connection()->getPdo()->prepare("CALL checkEmailVerified(?)");
            $stmt->execute(array($customerId));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }


    /**
     * check email verified or not
     * return success response or error response in json 
     * return id in data params
     */
    public function verifyEmail($emailVerifyKey)
    {

        try {
            $emailVerified = 0;
            $stmt = DB::connection()->getPdo()->prepare("CALL verifyEmail(?)");
            $stmt->execute(array($emailVerifyKey));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (isset($result['emailVerified'])) {
                $emailVerified = $result['emailVerified'];
            }
            return $emailVerified;
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Store device token
     * @param array $params
     * @throws Exception  
     * @return array of data
     */
    public function storeDeviceToken($params = [])
    {
        try {
            $inputData = json_encode($params);
            $pdo = DB::connection()->getPdo();
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
            $stmt = $pdo->prepare("CALL storeDeviceToken(?)");
            $stmt->execute([$inputData]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $stmt->closeCursor();
            $reponse = json_decode($result['response']);
            if ($reponse->status == "FAILURE" && $reponse->statusCode != 200) {
                return false;
            }
            return true;
        } catch (Exception $e) {
            return $this->sendError('Error.', $e->getMessage());
        }
    }

    /**
     * Specifies the user's FCM token
     *
     * @return string|array
     */
    public function routeNotificationForFcm($notification)
    {


        $data = $notification->data;
        // print_r($data['user_id']); exit;
        unset($notification->data['user_id']);
        if (is_array($data)) {
            //return CustomerDeviceTokens::select('device_token')->where('user_id', $data['user_id'])->first()->device_token;

            return CustomerDeviceTokens::select('device_token')->whereIn('user_id', $data['user_id'])->get()->pluck('device_token')->toArray();
        } else {
            return [];
        }
    }

    protected function changePassword($reqParams, $user)
    {
        if ($reqParams['password'] != $reqParams['password_confirmation']) {
            return ["status" => false, "message" => "New and confirm password should be same"];
        }

        if ($reqParams['password'] == $reqParams['old_password']) {
            return ["status" => false, "message" => "New and old password should not be same"];
        }

        if (Hash::check($reqParams['old_password'], $user->password)) {
            $input['password'] = bcrypt($reqParams['password']);
            $input['password_plain'] = DataHelper::encrypt($reqParams['password']);
            $user->update($input);
            return ["status" => true, "message" => "Password changed successfully"];
        }
        return ["status" => false, "message" => "Failed to change password"];
    }

    /**
     * Verify and Get referred by user
     * @param array $params
     * @throws Exception  
     * @return array of data
     */
    public function verifyAndGetReferredByUser($referralCouponCode)
    {
        try {
            $user = User::where('referral_code', $referralCouponCode)->first();
            if (!$user) {
                return ["status" => false];
            }
            return ["status" => true, "referred_by_user_id" => $user['id']];
        } catch (Exception $e) {
            return $this->sendError('Error.', $e->getMessage());
        }
    }

    /**
     * This function saves the sent push notification for future use
     * @param array $customerData
     */
    public static function saveUserLoginLogs($params)
    {
        try {
            $inputData = json_encode($params);
            $pdo = DB::connection()->getPdo();
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
            $stmt = $pdo->prepare("CALL storeUserLoginLogs(?)");
            $stmt->execute([$inputData]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $stmt->closeCursor();
            $reponse = json_decode($result['response']);
            if ($reponse->status == "FAILURE" && $reponse->statusCode != 200) {
                return false;
            }
            return true;
        } catch (Exception $e) {
            return $this->sendError('Error.', $e->getMessage());
        }
    }
}
