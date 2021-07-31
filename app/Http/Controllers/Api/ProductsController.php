<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Exception;
use Validator;
use Carbon\Carbon;

class ProductsController extends BaseController
{

    public function getProductList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'required',
            'category_id' => 'required',
            'sub_category_id' => 'required',
            'no_of_records' => 'required',
            'page_number' => 'required',
            // 'search_value' => 'required',
            // 'sort_type' => 'required',
            // 'sort_on' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError(parent::VALIDATION_ERROR, $validator->errors());
        }

        try {
            $params = [
                'platform' => $request->platform,
                'category_id' => $request->category_id,
                'sub_category_id' => $request->sub_category_id,
                'no_of_records' => $request->no_of_records,
                'page_number' => $request->page_number,
                'search_value' => $request->search_value,
                'sort_type' => $request->sort_type,
                'sort_on' => $request->sort_on,
            ];
            $params = json_encode($params);
            //Create product object to call functions
            $product = new Product();
            // Function call to get product list
            $responseDetails = $product->getProductList($params);
            $message = 'Product list.';
            if(sizeof($responseDetails) == 0) {
                $message = 'No record found.';
            }
            $response = $this->sendResponse($responseDetails, $message);
        } catch (Exception $e) {
            $response = $this->sendResponse(array(), $e->getMessage());
        }
        return $response;
        // $this->response->setContent(json_encode($response)); // send response in json format
    }

    public function storeProductInWishlist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'required',
            'user_id' => 'required|integer',
            'products_id' => 'required|integer',
            'is_basket' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return $this->sendError(parent::VALIDATION_ERROR, $validator->errors());
        }

        try {
            $params = [
                'platform' => $request->platform,
                'user_id' => $request->user_id,
                'products_id' => $request->products_id,
                'is_basket' => $request->is_basket
            ];

            //Create product object to call functions
            $product = new Product();
            // Function call to store product in wishlist
            $responseDetails = $product->storeProductInWishlist($params);
            if ($responseDetails["status"] == true) {
                return $this->sendResponse($responseDetails, $responseDetails["message"]);
            } else {
                return $this->sendError($responseDetails["message"], $responseDetails, 422);
            }
        } catch (Exception $e) {
            return $this->sendResponse(array(), $e->getMessage());
        }
    }

    public function removeProductFromWishlist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'required',
            'user_id' => 'required|integer',
            'wishlist_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return $this->sendError(parent::VALIDATION_ERROR, $validator->errors());
        }

        try {
            $params = [
                'platform' => $request->platform,
                'user_id' => $request->user_id,
                'wishlist_id' => $request->wishlist_id
            ];

            //Create product object to call functions
            $product = new Product();
            // Function call to remove product in wishlist
            $responseDetails = $product->removeProductFromWishlist($params);
            $message = 'Failed to remove product.';
            if ($responseDetails) {
                $message = 'Product removed successfully';
            }
            $response = $this->sendResponse([], $message);
        } catch (Exception $e) {
            $response = $this->sendResponse(array(), $e->getMessage());
        }
        return $response;
    }

    public function getWishlist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'platform' => 'required',
            'user_id' => 'required',
            'no_of_records' => 'required',
            'page_number' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError(parent::VALIDATION_ERROR, $validator->errors());
        }

        try {
            $params = [
                'platform' => $request->platform,
                'user_id' => $request->user_id,
                'no_of_records' => $request->no_of_records,
                'page_number' => $request->page_number
            ];
            $params = json_encode($params);
            //Create product object to call functions
            $product = new Product();
            // Function call to get wishlist list
            $responseDetails = $product->getWishlist($params);
            $message = 'Wishlist.';
            if(sizeof($responseDetails) == 0) {
                $message = 'No record found.';
            }
            $response = $this->sendResponse($responseDetails, $message);
        } catch (Exception $e) {
            $response = $this->sendResponse(array(), $e->getMessage());
        }
        return $response;
    }
}
