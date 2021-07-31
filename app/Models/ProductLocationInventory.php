<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

class ProductLocationInventory extends Model
{
    use SoftDeletes;

    public $table = 'product_location_inventory';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = ['product_units_id','current_quantity','created_by','updated_by'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    protected function getProductUnitCurrentQuantity($productUnitsId) {
        $productUnitCurQty = ProductLocationInventory::select('current_quantity')->where('product_units_id', $productUnitsId)->get()->toArray();
        return $productUnitCurQty[0]['current_quantity'];
    }

    protected function storeProductLocationInventory ($params, $productUnitId) {
        $inputs = $params->all();
        ProductLocationInventory::create(array(
            'product_units_id' => $productUnitId,
            'current_quantity' => $inputs['opening_quantity'],
            'created_by' => $inputs['created_by']
        ));
        return true;
    }
}
