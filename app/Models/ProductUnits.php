<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;
use App\Models\Product;
use App\Models\Unit;
use App\Models\ProductInventory;
use App\Models\ProductLocationInventory;
use DB;

class ProductUnits extends Model
{
    use SoftDeletes;

    public $table = 'product_units';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = ['products_id','unit_id','selling_price','special_price','special_price_start_date','special_price_end_date','opening_quantity','min_quantity','max_quantity','max_quantity_perday_percust','max_quantity_perday_allcust','notify_for_qty_below','status','created_by','updated_by'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id')->where('deleted_at', NULL);
    }
    
    public function baskets()
    {
        return $this->belongsToMany(Basket::class)->where('deleted_at', NULL);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'products_id')->where('deleted_at', NULL);
    }

    protected function getCurrentQuantity($productUnitsId) {
        $productUnitQty = ProductUnits::select('current_quantity')->where('product_units_id', $productUnitsId)->where('status', 1)->get()->toArray();
        return $productUnitQty[0]['current_quantity'];
    }

    protected function getProductUnitIds($productsId) {
        $unitIds = ProductUnits::select(DB::raw('GROUP_CONCAT(unit_id) AS ids'))->where('products_id', $productsId)->get()->toArray();
        return $unitIds[0]['ids'];
    }

    protected function getProductUnitById($productUnitId) {
        $product = ProductUnits::select('id','products_id','unit_id')->where('id', $productUnitId)->get()->toArray();
        return $product[0];
    }

    protected function storeInventory ($params) {
        $qty = ProductLocationInventory::select('id','current_quantity')->where('product_units_id', $params['product_unit_id'])->get()->toArray();
        $currentQuantity = $qty[0]['current_quantity'];
        if($params['inventory_type'] == 1) {
            $currentQuantity = $currentQuantity + $params['quantity'];
        } else {
            $currentQuantity = $currentQuantity - $params['quantity'];
        }
        $inventory = ProductLocationInventory::find($qty[0]['id']);
        $inventory->current_quantity = $currentQuantity;
        $inventory->save();

        $productUnit = ProductUnits::find($params['product_unit_id']);
        $productUnit->status = 1;
        $productUnit->save();

        ProductInventory::create(array(
            'product_units_id' => $params['product_unit_id'],
            'quantity' => ($params['inventory_type'] == 1) ? $params['quantity'] : '-'.$params['quantity'],
            'created_by' => 1
        ));
        return true;
    }
}
