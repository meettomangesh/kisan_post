<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

class ProductImages extends Model
{
    use SoftDeletes;

    public $table = 'product_images';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = ['products_id', 'image_name', 'image_description', 'display_order', 'status', 'created_by', 'updated_by'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    protected function getFirstImage($productId)
    {
        $firstProductImage = ProductImages::select('image_name')->where('products_id', $productId)->where('status', 1)->first();
        return ($firstProductImage) ? $firstProductImage->image_name : "";
    }
}
