<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ApiModel;
use PDO; 


class SystemEmail extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'system_emails';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'name', 'description', 'email_to', 'email_cc', 'email_bcc', 'email_from', 'subject', 'text1', 'text2', 'email_type', 'tags', 'status'];


}
