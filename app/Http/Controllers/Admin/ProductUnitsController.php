<?php

namespace App\Http\Controllers\Admin;

// use App\Product;
use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyProductUnitRequest;
use App\Http\Requests\StoreProductUnitRequest;
use App\Http\Requests\UpdateProductUnitRequest;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductUnits;
use App\Models\ProductInventory;
use App\Models\ProductLocationInventory;
use DB;

class ProductUnitsController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('product_unit_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        // $productUnits = ProductUnits::with('product','unit')->get();
        $productUnits = ProductUnits::all();
        return view('admin.product_units.index', compact('productUnits'));
    }

    public function create()
    {
        abort_if(Gate::denies('product_unit_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $products = Product::all()->where('is_basket', 0)->where('status', 1)->pluck('product_name', 'id')->prepend(trans('global.pleaseSelect'), '');
        return view('admin.product_units.create', compact('products'));
    }

    public function store(StoreProductUnitRequest $request)
    {
        $productUnit = ProductUnits::create($request->all());
        if ($productUnit->id > 0) {
            ProductInventory::storeProductInventory($request, $productUnit->id);
            ProductLocationInventory::storeProductLocationInventory($request, $productUnit->id);
        }
        return redirect()->route('admin.product_units.index');
    }

    public function edit(ProductUnits $productUnit)
    {
        abort_if(Gate::denies('product_unit_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('admin.product_units.edit', compact('productUnit'));
    }

    public function update(UpdateProductUnitRequest $request, ProductUnits $productUnit)
    {
        $productUnit->update($request->all());
        return redirect()->route('admin.product_units.index');
    }

    public function show(ProductUnits $productUnit)
    {
        abort_if(Gate::denies('product_unit_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('admin.product_units.show', compact('productUnit'));
    }

    public function destroy(ProductUnits $productUnit)
    {
        abort_if(Gate::denies('product_unit_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $productUnit->delete();
        return back();
    }

    public function massDestroy(MassDestroyProductUnitRequest $request)
    {
        ProductUnits::whereIn('id', request('ids'))->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }

    // public function getUnits($id)
    // {
    //     $productData = Product::getProductById($id);
    //     $categoryName = Category::getCategoryName($productData['category_id']);
    //     $unitIds = ProductUnits::getProductUnitIds($id);
    //     $unitIds = explode(',', $unitIds);
    //     $data['category'] = $categoryName;
    //     $data['units'] = DB::table("unit_master")->whereNotIn("id", $unitIds)->where("status", 1)->where("cat_id", $productData['category_id'])->pluck("unit", "id");
    //     $data['is_unit_available'] = sizeof($data['units']);
    //     return json_encode($data);
    // }

    public function getUnits($id)
    {
        $productData = Product::getProductById($id);
        $categoryName = Category::getCategoryName($productData['category_id']);
        $categoryArr = [];
        array_push($categoryArr, $productData['category_id']);
        $parentCatgory = Category::getParentCategory(array('category_id' => $productData['category_id']));
        //print_r($parentCatgory->cat_parent_id); exit;
         
        array_push($categoryArr, $parentCatgory->cat_parent_id);
        $unitIds = ProductUnits::getProductUnitIds($id);
        //echo $unitIds;
        $unitIds = ($unitIds) ? explode(',', $unitIds) : [];
        //echo count($unitIds);
        //print_r($categoryArr);exit;
        $data['category'] = $categoryName;
        //echo $productData['category_id'];
        if (count($unitIds) > 0) {
            $data['units'] = DB::table("unit_master")->whereNotIn("id", $unitIds)->where("status", 1)->whereIn("cat_id", $categoryArr)->pluck("unit", "id");
        } else {
            $data['units'] = DB::table("unit_master")->where("status", 1)->whereIn("cat_id", $categoryArr)->pluck("unit", "id");
        }
        $data['is_unit_available'] = sizeof($data['units']);
        return json_encode($data);
    }


    public function addOrRemoveInventory($productUnitId)
    {
        abort_if(Gate::denies('product_unit_add_or_remove_inventory'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $productUnit = ProductUnits::getProductUnitById($productUnitId);
        return view('admin.product_units.add_or_remove_inventory', compact('productUnit'));
    }

    public function storeInventory()
    {
        ProductUnits::storeInventory($_POST);
        return redirect()->route('admin.product_units.index');
    }
}
