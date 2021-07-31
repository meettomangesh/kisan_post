<?php

namespace App\Http\Requests;

use App\PinCode;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class MassDestroyPinCodeRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('pin_code_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'ids'   => 'required|array',
            'ids.*' => 'exists:pin_codes,id',
        ];
    }
}
