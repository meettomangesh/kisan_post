<?php

namespace App\Http\Requests;

use App\Role;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class StoreRegionRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('region_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'region_name'         => [
                'required',
                'unique:region_master',
            ],
            'pin_codes.*' => [
                'integer',
            ],
            'pin_codes'   => [
                'required',
                'array',
            ],
        ];
    }
}
