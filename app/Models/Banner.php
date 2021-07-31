<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \DateTimeInterface;

class Banner extends Model
{
    use SoftDeletes;

    public $table = 'banners';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = ['name','image_name','status','type','url','display_order','description','created_by','updated_by','created_at','updated_at','deleted_at'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    protected function storeBanner($params) {
        $image = $params->file('image_name');
        $inputs = $params->all();
        $path = '/images/banners/';
        $var = date_create();
        $time = date_format($var, 'YmdHis');
        $imageName = $time . '-' . $image->getClientOriginalName();
        $image->move(base_path().'/public' . $path, $imageName);
        Banner::create(array(
            'name' => $inputs['name'],
            'image_name' => $path.$imageName,
            'description' => $inputs['description'],
            'type' => $inputs['type'],
            'url' => $inputs['url'],
            'status' => $inputs['status'],
            'created_by' => $inputs['created_by']
        ));
    }

    protected function updateBanner($params, $banner) {
        $inputs = $params->all();
        $imageName = $banner->image_name;
        if ($params->hasFile('image_name')) {
            $image = $params->file('image_name');
            $path = '/images/banners/';
            $var = date_create();
            $time = date_format($var, 'YmdHis');
            $imageName = $time . '-' . $image->getClientOriginalName();
            $image->move(base_path().'/public' . $path, $imageName);
            $imageName = $path.$imageName;
            if(file_exists(public_path($banner->image_name))) {
                unlink(public_path($banner->image_name));
            }
        }
        $banner = Banner::find($banner->id);
        $banner->name = $inputs['name'];
        $banner->image_name = $imageName;
        $banner->description = $inputs['description'];
        $banner->type = $inputs['type'];
        $banner->url = $inputs['url'];
        $banner->status = $inputs['status'];
        $banner->updated_by = $inputs['updated_by'];
        $banner->save();
        return true;
    }

    public function getBannerList($type) {
        return Banner::select('id','name','image_name','type','url')->where('type', $type)->where('status', 1)->get()->toArray();
    }
}
