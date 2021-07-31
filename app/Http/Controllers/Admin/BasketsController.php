<?php

namespace App\Http\Controllers\Admin;

// use App\Product;
use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyBasketRequest;
use App\Http\Requests\StoreBasketRequest;
use App\Http\Requests\UpdateBasketRequest;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Basket;
use App\Models\ProductUnits;
use App\Models\Category;
use App\Models\Product;
use DB;

class BasketsController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('basket_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $baskets = Basket::all()->where('is_basket', 1);
        return view('admin.baskets.index', compact('baskets'));
    }

    public function create()
    {
        abort_if(Gate::denies('basket_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $categories = Category::all()->where('status', 1)->where('cat_parent_id', 0)->pluck('cat_name', 'id')->prepend(trans('global.pleaseSelect'), '');
        // $regions = Region::all()->where('status', 1)->pluck('region_name', 'id');
        $productUnits = ProductUnits::all()->where('status', 1);
        //->where('product.category_id',2);
        // echo '<pre>';
        // print_r($productUnits);
        // exit;
        return view('admin.baskets.create', compact('categories', 'productUnits'));
    }

    public function store(StoreBasketRequest $request)
    {
        if ($request->hasFile('basket_images')) {
            $basket = Basket::storeBasket($request);
            $basket->productUnits()->sync($request->input('productUnits', []));
        }
        return redirect()->route('admin.baskets.index');
    }

    public function edit(Basket $basket)
    {
        abort_if(Gate::denies('basket_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $productUnits = ProductUnits::all()->where('status', 1);
        $categories = Category::all()->where('status', 1)->pluck('cat_name', 'id')->prepend(trans('global.pleaseSelect'), '');

        $basketImages = Basket::getBasketImages($basket->id);
        $srNo = 0;
        $basket->load('productUnits');
        $basket->load('category');
        return view('admin.baskets.edit', compact('basket', 'productUnits', 'basketImages', 'srNo', 'categories'));
    }

    public function update(UpdateBasketRequest $request, Basket $basket)
    {
        $basket = Basket::updateBasket($request, $basket);
        $basket->productUnits()->sync($request->input('productUnits', []));
        return redirect()->route('admin.baskets.index');
    }

    public function show(Basket $basket)
    {
        abort_if(Gate::denies('basket_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $basket->load('productUnits');
        return view('admin.baskets.show', compact('basket'));
    }

    public function destroy(Basket $basket)
    {
        abort_if(Gate::denies('basket_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $basket->delete();
        return back();
    }

    public function massDestroy(MassDestroyBasketRequest $request)
    {

        Product::whereIn('id', request('ids'))->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function getProducts(Request $request)
    {
        $input = $request->all();
        if ($input['category_id'] && ( $input['category_name'] && $input['category_name'] != 'Basket') ) {
            $productUnits = ProductUnits::all()->where('status', 1)->where('product.category_id', $input['category_id']);
        } else {
            $productUnits = ProductUnits::all()->where('status', 1);
        }

        $productDetails = array();
        foreach($productUnits as $productUnit) {

            $productDetails[$productUnit->id]= $productUnit->product->product_name.' ('.$productUnit->unit->unit.') ';
        }

        $response['product_details'] = $productDetails;

        $response['status'] = '';
        $response['message'] = '';

        return response()->json($response);
    }
}
