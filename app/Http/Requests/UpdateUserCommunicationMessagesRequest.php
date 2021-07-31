<?php

namespace App\Http\Requests;

use App\User;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserCommunicationMessagesRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('communication_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            // 'first_name'    => [
            //     'required',
            // ],
            // 'last_name'    => [
            //     'required',
            // ],
            // 'email'   => [
            //     'required',
            //     'unique:users,email,' . request()->route('user')->id,
            // ],
            // 'mobile_number'   => [
            //     'required',
            //     'unique:users,mobile_number,' . request()->route('user')->id,
            // ],
            'roles.*' => [
                'integer',
            ],
            'regions.*' => [
                'integer',
            ],
        ];
    }
}
