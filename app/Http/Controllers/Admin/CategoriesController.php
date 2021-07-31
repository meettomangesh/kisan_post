<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyCategoryRequest;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoriesController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $categories = Category::all();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        abort_if(Gate::denies('category_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $categories = Category::all()->where('cat_parent_id', 0)->where('status', 1)->pluck('cat_name', 'id')->prepend(trans('global.pleaseSelect'), '');
        return view('admin.categories.create', compact('categories'));
    }

    public function store(StoreCategoryRequest $request)
    {
        // print_r($request->all()); exit;
        if ($request->hasFile('cat_image_name')) {
            // $category = Category::create($request->all());
            Category::storeCategory($request);
        }
        return redirect()->route('admin.categories.index');
    }

    public function edit(Category $category)
    {
        abort_if(Gate::denies('category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        // print_r($category); exit;
        $categories = Category::all()->where('cat_parent_id', 0)->where('status', 1)->pluck('cat_name', 'id')->prepend(trans('global.pleaseSelect'), '');
        return view('admin.categories.edit', compact('category','categories'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        // $category->update($request->all());
        Category::updateCategory($request, $category);
        return redirect()->route('admin.categories.index');
    }

    public function show(Category $category)
    {
        abort_if(Gate::denies('category_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('admin.categories.show', compact('category'));
    }

    public function destroy(Category $category)
    {
        abort_if(Gate::denies('category_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $category->delete();
        return back();
    }

    public function massDestroy(MassDestroyCategoryRequest $request)
    {
        Category::whereIn('id', request('ids'))->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
