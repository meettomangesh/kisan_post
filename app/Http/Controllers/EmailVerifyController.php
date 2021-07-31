<?php

namespace App\Http\Controllers;

use Input;
use App\User;
use Illuminate\Http\Request;

class EmailVerifyController extends Controller
{

    /**
     * Verify Email Address.
     *
     * @return view as response
     */
    public function verifyEmail(Request $request)
    {
        $params = $request->all();
        
        $emailVerifyKey = $params['key'];
        $emailVerified = 0;
        $user = new User();
        if (isset($emailVerifyKey) && !empty($emailVerifyKey)) {
            $emailVerified = $user->verifyEmail($emailVerifyKey);
        }
        return view('verify', compact('emailVerified'));
    }
}
