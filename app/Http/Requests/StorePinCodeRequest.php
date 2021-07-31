<?php

namespace App\Http\Requests;

use App\City;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class StorePinCodeRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('pin_code_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'pin_code'       => [
                'required',
            ],
            'country_id' => [
                'required',
                'integer',
            ],
            'state_id' => [
                'required',
                'integer',
            ],
            'city_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
