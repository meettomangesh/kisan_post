<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;
use App\Models\Category;

class Unit extends Model
{
    use SoftDeletes;

    public $table = 'unit_master';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = ['cat_id','unit','status','description','created_by','updated_by','created_at','updated_at','deleted_at'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'cat_id');
    }

    protected function getUnitName($id) {
        $unitName = Unit::select('unit')->where('id', $id)->where('status', 1)->get()->toArray();
        if(!empty($unitName[0])) {
            return $unitName[0]['unit'];
        }
        return '';
    }
}
