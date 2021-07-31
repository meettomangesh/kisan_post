<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Models\ApiModel;
use PDO; 
class PushNotificationsTemplates extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'push_notifications_templates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'name', 'title', 'message', 'deeplink', 'status'];

}
