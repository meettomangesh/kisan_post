<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

class CampaignCategoriesMaster extends Model
{
    use SoftDeletes;

    public $table = 'campaign_categories_master';

    protected $dates = [
        'created_at',
        'deleted_at',
    ];

    protected $fillable = ['name','description','status','created_by','updated_by'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
