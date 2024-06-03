<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
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
		$random_text = 'cafewebindonesia' . date('Y') . 'faizmsyam' . sha1('kadaku-app') . strtotime(date('Y-m-d'));
		if (!isset($_GET['ref'])) return redirect('panel-admin-kadaku?ref=fms' . date('Y') . '&signature=' . sha1($random_text));

		$ref = isset($_GET['ref']) ? $_GET['ref'] : '';
		$signature = isset($_GET['signature']) ? $_GET['signature'] : '';
		$lock_ref = 'fms' . date('Y');
		$lock_signature = sha1($random_text);
		$locked = false;
		if (($ref !== $lock_ref) || ($signature !== $lock_signature)) {
			$locked = true;
		}

		if ($locked) {
			return view('home.index');
		} else {
			return view('auth.index');
		}
	}

	function login(LoginRequest $request)
	{
		$validator = Validator::make($request->all(), [
			'username' => 'required|string|max:255|email',
			'password' => 'required|string',
		]);

		$redirect_to = 'panel-admin-kadaku';

		if ($validator->fails()) {
			return redirect()->to($redirect_to)->withErrors($validator);
		}

		$credentials = $request->getCredentials();
		if (!Auth::validate($credentials)) :
			return redirect()->to($redirect_to)->withErrors(trans('auth.failed'));
		endif;
		
		$user = Auth::getProvider()->retrieveByCredentials($credentials);
		if ($user) {
			if ($user->is_active == 0) {
				return redirect($redirect_to)->with('unauthorized', 'Akun anda sudah tidak aktif.');
			}

			Auth::guard('admin')->login($user);
			return $this->authenticated($request, $user);
		} else {
			return redirect($redirect_to)->with('unauthorized', 'Anda tidak diizinkan untuk Login.');
		}
	}

	protected function authenticated(Request $request, $user)
	{
		return redirect('dashboard');
	}
}
