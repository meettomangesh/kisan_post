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
use App\Models\BasketProduct;
use App\Models\CustomerWishlist;
use App\User;
use DB;
use PDO;

class Product extends Model
{
    use SoftDeletes;

    public $table = 'products';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = ['brand_id', 'category_id', 'product_name', 'short_description', 'sku', 'expiry_date', 'stock_availability', 'status', 'view_count','created_by','updated_by'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /* public function basketProduct() {
        return $this->hasMany(BasketProduct::class, 'basket_id');
    } */

    protected function storeProductImages ($params, $productId, $flag) {
        $images = $params->file('product_images');
        $inputs = $params->all();
        $userId = 0;
        if($flag == 1) {
            $userId = $inputs['created_by'];
        } elseif($flag == 2) {
            $userId = $inputs['updated_by'];
        }
        if ($params->hasFile('product_images')) {
            $path = '/images/products/';
            $i = 0;
            foreach ($images as $item) {
                $var = date_create();
                $time = date_format($var, 'YmdHis');
                $imageName = $time . '-' . $item->getClientOriginalName();
                $item->move(base_path().'/public' . $path, $imageName);
                ProductImages::create(array(
                    'products_id' => $productId,
                    'image_name' => $path.$imageName,
                    'display_order' => $i,
                    'created_by' => $userId
                ));
                $i++;
            }
        }
        return true;
    }

    protected function getProductImages($productId) {
        return ProductImages::select('id','image_name')->where('products_id', $productId)->get();
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

    protected function storeProductUnits ($params, $productId, $flag) {
        $inputs = $params->all();
        $productUnits = $inputs['unit_ids'];
        $userId = 0;
        if($flag == 1) {
            $userId = $inputs['created_by'];
        } elseif($flag == 2) {
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

    protected function removeProductUnits($productId) {
        ProductUnits::where('products_id',$productId)->delete();
        return true;
    }

    protected function getProductById($productId) {
        $product = Product::select('id','product_name','category_id')->where('id', $productId)->get()->toArray();
        if(!empty($product[0])) {
            return $product[0];
        }
        return [];
    }

    protected function getProductName($productId) {
        $productName = Product::select('product_name')->where('id', $productId)->where('status', 1)->get()->toArray();
        if(!empty($productName[0])) {
            return $productName[0]['product_name'];
        }
        return '';
    }

    protected function getProductUnits($productId) {
        return ProductUnits::join('unit_master', 'unit_master.id', '=', 'product_units.unit_id')
            ->where('product_units.status', 1)
            ->where('product_units.products_id', $productId)
            ->get(['unit_master.id','unit_master.unit']);
            // ->toArray();
    }

    /**
     * Get product list
     * @param array $params
     * @throws Exception  
     * @return array of data
     */
    public function getProductList($params) {
        $queryResult = DB::select('call getProductList(?)', [$params]);
        // $result = collect($queryResult);
        if(sizeof($queryResult) > 0) {
            foreach($queryResult as $key => $val) {
                if($val->is_basket == 0) {
                    $productUnits = ProductUnits::join('unit_master', 'unit_master.id', '=', 'product_units.unit_id')
                        ->join('product_location_inventory', 'product_location_inventory.product_units_id', '=', 'product_units.id')
                        ->where('product_units.status', 1)
                        ->where('product_units.products_id', $val->id)
                        ->where('product_units.selling_price', '>', 0)
                        ->where('product_location_inventory.current_quantity', '>', 0)
                        // ->get(['product_units.id','unit_master.unit','TRUNCATE(product_units.selling_price, 2) AS selling_price','product_units.special_price','product_units.min_quantity','product_units.max_quantity','product_location_inventory.current_quantity'])
                        ->select(DB::raw('product_units.id, unit_master.unit, TRUNCATE(product_units.selling_price, 2) AS selling_price, IF(product_units.special_price > 0 AND product_units.special_price_start_date <= CURDATE() AND product_units.special_price_end_date >= CURDATE(), TRUNCATE(product_units.special_price, 2), 0.00)  AS special_price, product_units.special_price_start_date, product_units.special_price_end_date, product_units.min_quantity, product_units.max_quantity, product_location_inventory.current_quantity'))
                        ->get()->toArray();
                    if(sizeof($productUnits) > 0) {
                        $queryResult[$key]->product_units = $productUnits;
                        $queryResult[$key]->product_images = ProductImages::select('image_name')->where('products_id', $val->id)->where('status', 1)->get()->toArray();
                    } else {
                        unset($queryResult[$key]);
                    }
                } else {
                    $basketData['basket_id'] = $val->id;
                    $inputData = json_encode($basketData);
                    $pdo = DB::connection()->getPdo();
                    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
                    $stmt = $pdo->prepare("CALL validateBasketProducts(?)");
                    $stmt->execute([$inputData]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                    $stmt->closeCursor();
                    $reponse = json_decode($result['response']);
                    if($reponse->status == "FAILURE" && $reponse->statusCode != 200) {
                        unset($queryResult[$key]);
                    } else {
                        $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
                        $queryResult[$key]->product_units = array();
                        $queryResult[$key]->product_images = ProductImages::select('image_name')->where('products_id', $val->id)->where('status', 1)->get()->toArray();
                    }
                }
            }
        }
        $queryResult = array_merge($queryResult);
        return $queryResult;
    }
    
     /**
     * Store product in wishlist
     * @param array $params
     * @throws Exception  
     * @return array of data
     */
    public function storeProductInWishlist($params)
    {
        try {
            $inputData = json_encode($params);
            $pdo = DB::connection()->getPdo();
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
            $stmt = $pdo->prepare("CALL storeProductInWishlist(?)");
            $stmt->execute([$inputData]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $stmt->closeCursor();
            $reponse = json_decode($result['response']);
            if ($reponse->status == "FAILURE" && $reponse->statusCode != 200) {
                return ["status" => false, "message" => $reponse->message];
            }
            return ["status" => true, "message" => $reponse->message, "wishlist_id" => $reponse->data->wishlist_id];
        } catch (Exception $e) {
            return $this->sendError('Error.', $$e->getMessage());
        }
    }

    /**
     * Remove product from wishlist
     * @param array $params
     * @throws Exception  
     * @return array of data
     */
    public function removeProductFromWishlist($params)
    {
        try {
            CustomerWishlist::where('id', $params['wishlist_id'])->forceDelete();
            return true;
        } catch (Exception $e) {
            return $this->sendError('Error.', $$e->getMessage());
        }
    }

    /**
     * Get wishlist
     * @param array $params
     * @throws Exception  
     * @return array of data
     */
    public function getWishlist($params) {
        $queryResult = DB::select('call getWishlist(?)', [$params]);
        if(sizeof($queryResult) > 0) {
            foreach($queryResult as $key => $val) {
                if($val->is_basket == 0) {
                    $productUnits = ProductUnits::join('unit_master', 'unit_master.id', '=', 'product_units.unit_id')
                        ->join('product_location_inventory', 'product_location_inventory.product_units_id', '=', 'product_units.id')
                        ->where('product_units.products_id', $val->id)
                        ->select(DB::raw('product_units.id, unit_master.unit, TRUNCATE(product_units.selling_price, 2) AS selling_price, IF(product_units.special_price > 0 AND product_units.special_price_start_date <= CURDATE() AND product_units.special_price_end_date >= CURDATE(), TRUNCATE(product_units.special_price, 2), 0.00)  AS special_price, product_units.special_price_start_date, product_units.special_price_end_date, product_units.min_quantity, product_units.max_quantity, product_location_inventory.current_quantity,
                        IF(product_units.status = 1 AND product_location_inventory.current_quantity > 0 AND product_units.selling_price > 0, 1, 0) AS is_active
                        '))
                        ->get()->toArray();
                    $queryResult[$key]->product_units = $productUnits;
                    $queryResult[$key]->product_images = ProductImages::select('image_name')->where('products_id', $val->id)->where('status', 1)->get()->toArray();
                    if($queryResult[$key]->is_active == 1) {
                        foreach($productUnits as $puKey => $puVal) {
                            if($puVal['is_active'] == 0) {
                                $queryResult[$key]->is_active = $puVal['is_active'];
                            }
                        }
                    }
                } else {
                    if($queryResult[$key]->is_active == 1) {
                        $basketData['basket_id'] = $val->id;
                        $inputData = json_encode($basketData);
                        $pdo = DB::connection()->getPdo();
                        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
                        $stmt = $pdo->prepare("CALL validateBasketProducts(?)");
                        $stmt->execute([$inputData]);
                        $result = $stmt->fetch(PDO::FETCH_ASSOC);
                        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                        $stmt->closeCursor();
                        $reponse = json_decode($result['response']);
                        if($reponse->status == "FAILURE" && $reponse->statusCode != 200) {
                            $queryResult[$key]->is_active = 0;
                        }
                    } 
                    $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
                    $queryResult[$key]->product_units = array();
                    $queryResult[$key]->product_images = ProductImages::select('image_name')->where('products_id', $val->id)->where('status', 1)->get()->toArray();
                }
            }
        }
        $queryResult = array_merge($queryResult);
        return $queryResult;
    }
}
