<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

class ConfigSettings extends Model
{
    use SoftDeletes;

    public $table = 'config_settings';

    protected $dates = [
        'created_at',
        'deleted_at'
    ];

    protected $fillable = ['name','value','created_by','deleted_at'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
