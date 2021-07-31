<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;
use App\Models\ProductImages;
use App\Models\ProductInventory;
use App\Models\ProductLocationInventory;
use App\Models\Category;
use App\Models\ProductUnits;
use App\User;
use App\Helper\DataHelper;
use Illuminate\Support\Facades\File;
use DB;

class Basket extends Model
{
    use SoftDeletes;

    public $table = 'products';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $fillable = [
        'product_name',
        'description',
        'category_id',
        'short_description',
        'sku',
        'images',
        'selling_price',
        'special_price',
        'special_price_start_date',
        'special_price_end_date',
        'opening_quantity',
        'min_quantity',
        'max_quantity',
        'status',
        'created_by',
        'updated_by'
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // public function pin_codes()
    // {
    //     return $this->belongsToMany(PinCode::class);
    // }

    protected function storeBasket($params)
    {
        $params->brand_id = 0;
        $params->category_id = 0;
        $basket = self::create($params->all());

        //$imagePath = DataHelper::uploadImage($params->file('basket_images'), '/images/baskets/',$basket->id);
        //$basket->images = $imagePath;
        $this->storeBasketImages($params, $basket->id, 1);
        $basket->is_basket = 1;
        $basket->brand_id = 0;
        //$basket->category_id = 0;
        $basket->update();
        return $basket;
    }

    protected function updateBasket($params, $basket)
    {
        $params->brand_id = 0;
        $params->category_id = 0;
        $inputs = $params->all();
        $basket->update($inputs);
        // $imagePath = $basket->images;
        // if ($params->hasFile('images')) {
        //     $imagePath = DataHelper::uploadImage($params->file('images'), '/images/baskets/', $basket->id);
        //     if (file_exists(public_path($basket->images))) {
        //         unlink(public_path($basket->images));
        //     }
        // }
        //$basket->images = $imagePath;
        if ($params->hasFile('basket_images') && $basket->id > 0) {
            $this->storeBasketImages($params, $basket->id, 2);
        }

        if($inputs['removed_images'] != '' && $basket->id > 0) {
            $this->removeProductImages($inputs['removed_images']);
        }
        $basket->is_basket = 1;
        $basket->brand_id = 0;
        //$basket->category_id = 0;
        $basket->update();
        return $basket;
    }


    protected function removeProductImages($ids) {
        $imageIds = explode(',', $ids);
        foreach($imageIds as $key => $val) {
            $prodImage = ProductImages::select('id','image_name','display_order')->where('id', $val)->get()->toArray();
            if(isset($prodImage[0]['image_name']) && file_exists(public_path($prodImage[0]['image_name']))) {
                unlink(public_path($prodImage[0]['image_name']));
            }
            ProductImages::destroy($val);
        }
        return true;
    }

    protected function storeBasketImages($params, $productId, $flag)
    {
        $images = $params->file('basket_images');
        $inputs = $params->all();
        $userId = 0;
        if ($flag == 1) {
            $userId = $inputs['created_by'];
        } elseif ($flag == 2) {
            $userId = $inputs['updated_by'];
        }
        if ($params->hasFile('basket_images')) {
            // $path = '/images/baskets/';
            // if ($productId != 0) {
            //     $path .= $productId;
            //     if (!File::exists(public_path() . $path)) {
            //         File::makeDirectory(public_path() . $path, 0775, true);
            //     }
            //     $path .= '/';
            // }


            $i = 0;
            foreach ($images as $item) {
                // $var = date_create();
                // $time = date_format($var, 'YmdHis');
                // $imageName = $time . '-' . $item->getClientOriginalName();
                // $item->move(base_path() . '/public' . $path, $imageName);
                $imagePath = DataHelper::uploadImage($item, '/images/baskets/',$productId);
       
                ProductImages::create(array(
                    'products_id' => $productId,
                    'image_name' => $imagePath,
                    'display_order' => $i,
                    'created_by' => $userId
                ));
                $i++;
            }
        }
        return true;
    }

    protected function getBasketImages($productId)
    {
        return ProductImages::select('id', 'image_name')->where('products_id', $productId)->get();
    }

    protected function removeBasketImages($ids)
    {
        $imageIds = explode(',', $ids);
        foreach ($imageIds as $key => $val) {
            $prodImage = ProductImages::select('id', 'image_name', 'display_order')->where('id', $val)->get()->toArray();
            if (isset($prodImage[0]['image_name']) && file_exists(public_path($prodImage[0]['image_name']))) {
                unlink(public_path($prodImage[0]['image_name']));
            }
            ProductImages::destroy($val);
        }
        return true;
    }

    protected function storeProductUnits($params, $productId, $flag)
    {
        $inputs = $params->all();
        $productUnits = $inputs['unit_ids'];
        $userId = 0;
        if ($flag == 1) {
            $userId = $inputs['created_by'];
        } elseif ($flag == 2) {
            $userId = $inputs['updated_by'];
        }
        foreach ($productUnits as $item) {
            ProductUnits::create(array(
                'products_id' => $productId,
                'unit_id' => $item,
                'created_by' => $userId
            ));
        }
        return true;
    }

    protected function removeProductUnits($productId)
    {
        ProductUnits::where('products_id', $productId)->delete();
        return true;
    }

    protected function getProductById($productId)
    {
        $product = Product::select('id', 'product_name', 'category_id')->where('id', $productId)->get()->toArray();
        if (!empty($product[0])) {
            return $product[0];
        }
        return [];
    }

    protected function getProductName($productId)
    {
        $productName = Product::select('product_name')->where('id', $productId)->where('status', 1)->get()->toArray();
        if (!empty($productName[0])) {
            return $productName[0]['product_name'];
        }
        return '';
    }

    protected function getProductUnits($productId)
    {
        return ProductUnits::join('unit_master', 'unit_master.id', '=', 'product_units.unit_id')
            ->where('product_units.status', 1)
            ->where('product_units.products_id', $productId)
            ->get(['unit_master.id', 'unit_master.unit']);
        // ->toArray();
    }

    public function getProductList($params)
    {
        $queryResult = DB::select('call getProductList(?)', [$params]);
        // $result = collect($queryResult);
        if (sizeof($queryResult) > 0) {
            foreach ($queryResult as $key => $val) {
                $productUnits = ProductUnits::join('unit_master', 'unit_master.id', '=', 'product_units.unit_id')
                    ->join('product_location_inventory', 'product_location_inventory.product_units_id', '=', 'product_units.id')
                    ->where('product_units.status', 1)
                    ->where('product_units.products_id', $val->id)
                    ->where('product_units.selling_price', '>', 0)
                    ->where('product_location_inventory.current_quantity', '>', 0)
                    // ->get(['product_units.id','unit_master.unit','TRUNCATE(product_units.selling_price, 2) AS selling_price','product_units.special_price','product_units.min_quantity','product_units.max_quantity','product_location_inventory.current_quantity'])
                    ->select(DB::raw('product_units.id, unit_master.unit, TRUNCATE(product_units.selling_price, 2) AS selling_price, IF(product_units.special_price > 0 AND product_units.special_price_start_date <= CURDATE() AND product_units.special_price_end_date >= CURDATE(), TRUNCATE(product_units.special_price, 2), 0.00)  AS special_price, product_units.special_price_start_date, product_units.special_price_end_date, product_units.min_quantity, product_units.max_quantity, product_location_inventory.current_quantity'))
                    ->get()
                    ->toArray();
                if (sizeof($productUnits) > 0) {
                    $queryResult[$key]->product_units = $productUnits;
                    $queryResult[$key]->product_images = ProductImages::select('image_name')->where('products_id', $val->id)->where('status', 1)->get()->toArray();
                } else {
                    unset($queryResult[$key]);
                }
            }
            $queryResult = array_merge($queryResult);
        }
        return $queryResult;
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'products_id');
    }
    public function productUnits()
    {
        return $this->belongsToMany(ProductUnits::class);
    }

    protected function getCurrentQuantity($productUnitsId)
    {
        $productUnitQty = ProductUnits::select('current_quantity')->where('product_units_id', $productUnitsId)->where('status', 1)->get()->toArray();
        return $productUnitQty[0]['current_quantity'];
    }

    protected function getProductUnitIds($productsId)
    {
        $unitIds = ProductUnits::select(DB::raw('GROUP_CONCAT(unit_id) AS ids'))->where('products_id', $productsId)->get()->toArray();
        return $unitIds[0]['ids'];
    }

    protected function getProductUnitById($productUnitId)
    {
        $product = ProductUnits::select('id', 'products_id', 'unit_id')->where('id', $productUnitId)->get()->toArray();
        return $product[0];
    }

    protected function storeInventory($params)
    {
        $qty = ProductLocationInventory::select('id', 'current_quantity')->where('product_units_id', $params['product_unit_id'])->get()->toArray();
        $currentQuantity = $qty[0]['current_quantity'];
        if ($params['inventory_type'] == 1) {
            $currentQuantity = $currentQuantity + $params['quantity'];
        } else {
            $currentQuantity = $currentQuantity - $params['quantity'];
        }
        $inventory = ProductLocationInventory::find($qty[0]['id']);
        $inventory->current_quantity = $currentQuantity;
        $inventory->save();

        ProductInventory::create(array(
            'product_units_id' => $params['product_unit_id'],
            'quantity' => ($params['inventory_type'] == 1) ? $params['quantity'] : '-' . $params['quantity'],
            'created_by' => 1
        ));
        return true;
    }
}
