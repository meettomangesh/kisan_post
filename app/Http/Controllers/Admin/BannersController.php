<?php

namespace App\Http\Controllers\Admin;

use App\Models\Banner;
use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyBannerRequest;
use App\Http\Requests\StoreBannerRequest;
use App\Http\Requests\UpdateBannerRequest;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BannersController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('banner_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $banners = Banner::all();
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        abort_if(Gate::denies('banner_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('admin.banners.create');
    }

    public function store(StoreBannerRequest $request)
    {
        // print_r($request->all()); exit;
        if ($request->hasFile('image_name')) {
            Banner::storeBanner($request);
        }
        return redirect()->route('admin.banners.index');
    }

    public function edit(Banner $banner)
    {
        abort_if(Gate::denies('banner_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(UpdateBannerRequest $request, Banner $banner)
    {
        Banner::updateBanner($request, $banner);
        return redirect()->route('admin.banners.index');
    }

    public function show(Banner $banner)
    {
        abort_if(Gate::denies('banner_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return view('admin.banners.show', compact('banner'));
    }

    public function destroy(Banner $banner)
    {
        abort_if(Gate::denies('banner_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $banner->delete();
        return back();
    }

    public function massDestroy(MassDestroyBannerRequest $request)
    {
        Banner::whereIn('id', request('ids'))->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
