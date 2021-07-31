<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;


use DB;
use PDO;

class PromoCodeFormat extends Model
{
    use SoftDeletes;

    public $table = 'promo_code_format_master';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = ['promo_code_master_id', 'code_format', 'code_prefix', 'code_suffix', 'code_length', 'code_sample','status', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

}
