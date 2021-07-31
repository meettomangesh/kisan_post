<?php

namespace App\Http\Controllers\Admin;

// use App\Product;
use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyProductRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Product;
use App\Models\ProductImages;
use App\Models\Category;
use App\Models\Unit;
use DB;

class ProductsController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('product_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $products = Product::all()->where('is_basket', 0);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        abort_if(Gate::denies('product_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $categories = Category::all()->where('status', 1)->where('cat_parent_id', 0)->pluck('cat_name', 'id')->prepend(trans('global.pleaseSelect'), '');
        return view('admin.products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request)
    {
        if ($request->hasFile('product_images')) {
            $productData = $request->all();
            $productData['category_id'] = $productData['sub_category_id'];
            // $product = Product::create($request->all());
            $product = Product::create($productData);
            if($product->id > 0) {
                Product::storeProductImages($request, $product->id, 1);
            }
        }
        return redirect()->route('admin.products.index');
    }

    public function edit(Product $product)
    {
        abort_if(Gate::denies('product_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $productImages = Product::getProductImages($product->id);
        $srNo = 0;
        $categories = Category::all()->where('status', 1)->pluck('cat_name', 'id')->prepend(trans('global.pleaseSelect'), '');
        $product->load('category');
        return view('admin.products.edit', compact('product','productImages','srNo','categories'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $requestAll = $request->all();
        $product->update($requestAll);
        if ($request->hasFile('product_images') && $product->id > 0) {
            Product::storeProductImages($request, $product->id, 2);
        }
        if($requestAll['removed_images'] != '' && $product->id > 0) {
            Product::removeProductImages($requestAll['removed_images']);
        }
        return redirect()->route('admin.products.index');
    }

    public function show(Product $product)
    {
        abort_if(Gate::denies('product_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('admin.products.show', compact('product'));
    }

    public function destroy(Product $product)
    {
        abort_if(Gate::denies('product_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $product->delete();
        return back();
    }

    public function massDestroy(MassDestroyProductRequest $request)
    {
        Product::whereIn('id', request('ids'))->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function getSubCategories($id)
    {
        $data['sub_categories'] = DB::table("categories_master")->where("cat_parent_id", $id)->where("status", 1)->pluck("cat_name", "id");
        $data['is_sub_category_available'] = sizeof($data['sub_categories']);
        return json_encode($data);
    }

}
