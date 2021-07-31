<?php

namespace App\Http\Requests;

use App\Models\Category;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class StoreCategoryRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('category_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;
    }

    public function rules()
    {
        $this->sanitize();
        return [
            'cat_name' => ['required','max:50','unique:categories_master'], //alphaSpaces
            'status' => ['required','numeric']
        ];
    }

    public function sanitize()
    {
        $input = $this->all();
        $input['cat_name'] = filter_var($input['cat_name'], FILTER_SANITIZE_STRING);
        $input['cat_description'] = filter_var($input['cat_description'], FILTER_SANITIZE_STRING);
        $input['created_by'] = Auth::id();
        $this->merge($input);
    }
}
