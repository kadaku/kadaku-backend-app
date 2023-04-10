<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Models\BrandModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    function show()
    {
        return view('auth.index');
    }

    function login(LoginRequest $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->to('login')->withErrors($validator);
        }

        $credentials = $request->getCredentials();
        if(!Auth::validate($credentials)):
            return redirect()->to('login')->withErrors(trans('auth.failed'));
        endif;
        $user = Auth::getProvider()->retrieveByCredentials($credentials);
        Auth::guard('admin')->login($user);   
        return $this->authenticated($request, $user);
    }

    protected function authenticated(Request $request, $user) 
    {
        return redirect()->intended();
    }
}
