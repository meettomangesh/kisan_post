<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

class CampaignMaster extends Model
{
    use SoftDeletes;

    public $table = 'campaign_master';

    protected $dates = [
        'created_at',
        'deleted_at',
    ];

    protected $fillable = ['name','campaign_category_id','status','created_by','updated_by'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
