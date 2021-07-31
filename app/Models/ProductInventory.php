<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

class ProductInventory extends Model
{
    use SoftDeletes;

    public $table = 'product_inventory';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = ['product_units_id','quantity','created_by','updated_by'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    protected function storeProductInventory ($params, $productUnitId) {
        $inputs = $params->all();
        ProductInventory::create(array(
            'product_units_id' => $productUnitId,
            'quantity' => $inputs['opening_quantity'],
            'created_by' => $inputs['created_by']
        ));
        return true;
    }
}
