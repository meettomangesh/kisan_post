<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\User;

class ChangePasswordController extends Controller
{
    public function edit()
    {
        abort_if(Gate::denies('profile_password_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('auth.passwords.edit');
    }

    public function update(UpdatePasswordRequest $request)
    {
        // auth()->user()->update($request->validated());
        $response = User::changePassword($request->all(), auth()->user());
        if($response['status']) {
            return redirect()->route('profile.password.edit')->with('message', __('global.change_password_success'));
        } else {
            return redirect()->route('profile.password.edit')->withErrors([$response['message']]);
        }
    }
}
