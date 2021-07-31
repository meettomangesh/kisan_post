<?php

namespace App\Http\Requests;

use App\Models\Banner;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class StoreBannerRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('banner_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;
    }

    public function rules()
    {
        $this->sanitize();
        return [
            'name' => ['required','max:50','unique:banners'], //alphaSpaces
            'type' => ['required','numeric'],
            'status' => ['required','numeric']
        ];
    }

    public function sanitize()
    {
        $input = $this->all();
        $input['name'] = filter_var($input['name'], FILTER_SANITIZE_STRING);
        $input['description'] = filter_var($input['description'], FILTER_SANITIZE_STRING);
        $input['created_by'] = Auth::id();
        $this->merge($input);
    }
}
