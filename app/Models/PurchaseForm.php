<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

class PurchaseForm extends Model
{
    use SoftDeletes;

    public $table = 'purchase_form';

    protected $dates = [
        'created_at',
        'deleted_at',
    ];

    protected $fillable = ['supplier_name','product_name','unit','category','price','order_date','total_in_kg','total_units','created_by','created_at','deleted_at'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
