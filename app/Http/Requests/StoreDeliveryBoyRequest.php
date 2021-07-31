<?php

namespace App\Http\Requests;

use App\User;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class StoreDeliveryBoyRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('deliveryboy_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'first_name'     => [
                'required',
            ],
            'last_name'     => [
                'required',
            ],
            'email'    => [
                'required',
                //'unique:users',
            ],
            'mobile_number' => [
                'required',
                'unique:users',
                'digits:10',
                'regex:/^([0-9\s\-\+\(\)]*)$/',
                'min:10',
            ],
            'password' => [
                'required',
            ],
            'roles.*'  => [
                'integer',
            ],
            'roles'    => [
                'required',
                'array',
            ],
            'regions.*'  => [
                'integer',
            ],
            'regions'    => [
                'required',
                'array',
            ],
        ];
    }
}
