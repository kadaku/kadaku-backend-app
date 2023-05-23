<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\API\CustomerSocialModel;
use App\Models\AuthModel;
use Exception;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    
    function __construct()
    {
        // $this->middleware(['social']);
    }
    
    function redirect($service)
    {
        return Socialite::driver($service)->stateless()->redirect();
    }

    function callback($service)
    {
        try {
            $serviceUser = Socialite::driver($service)->stateless()->user();
        } catch (Exception $e) {
            return redirect(env('APP_URL_FRONTEND') . '/auth/social-callback?error=Unable to login using ' . $service . '. Please try again' . '&origin=login');
        }

        $email = $serviceUser->getEmail();
        if ($service != 'google') {
            $email = $serviceUser->getId() . '@' . $service . '.local';
        }

        $user = $this->getExistingUser($serviceUser, $email, $service);
        $newUser = false;
        if (!$user) {
            $newUser = true;
            $param = [
                'name' => $serviceUser->getName(),
                'email' => $email,
                'email_verified_at' => now(),
                'password' => '',
                'phone_code' => '',
                'phone_dial_code' => '',
                'phone' => '',
                'avatar' => $serviceUser->getAvatar(),
                'is_active' => 1,
            ];
            $user = AuthModel::create($param);
        }

        if ($this->needToCreateSocial($user, $service)) {
            $param = [
                'customer_id' => $user->id,
                'service_id' => $serviceUser->id,
                'service_name' => $service,
            ];
            CustomerSocialModel::create($param);
        }

        $token = $user->createToken('login_token', ['*'], now()->addHours(5))->plainTextToken;
        // return redirect(env('APP_URL_FRONTEND') . '/auth/social-callback?token=' . $token . '&origin=' . ($newUser ? 'register' : 'login'));
        return redirect('http://192.168.0.221:3000/auth/social-callback?token=' . $token . '&origin=' . ($newUser ? 'register' : 'login'));
    }

    function needToCreateSocial(AuthModel $user, $service)
    {
        return !$user->hasSocialLinked($service);
    }

    function getExistingUser($serviceUser, $email, $service)
    {
        if ($service == 'google') {
            return AuthModel::where('email', $email)->orWhereHas('social', function($q) use ($serviceUser, $service) {
                $q->where('service_id', $serviceUser->getId())->where('service_name', $service);
            })->first();
        } else {
            $userSocial = CustomerSocialModel::where('service_id', $serviceUser->getId())->first();
            return $userSocial ? $userSocial->user : null;
        }
    }
}
