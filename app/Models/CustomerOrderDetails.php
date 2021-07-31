<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;
use App\Models\CustomerOrders;

class CustomerOrderDetails extends Model
{
    use SoftDeletes;

    public $table = 'customer_order_details';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = ['customer_id','order_id','products_id','product_units_id','item_quantity','expiry_date','selling_price','special_price','special_price_start_date','special_price_end_date','reject_cancel_reason','is_basket','order_status','created_by','updated_by','created_at','updated_at'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function customerOrder()
    {
        return $this->belongsTo(CustomerOrders::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'products_id');
    }

    public function productUnit()
    {
        return $this->belongsTo(ProductUnits::class, 'product_units_id');
    }
}
