<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\VerifyEmail;
use App\Models\API\CustomerSocialModel;
use App\Models\API\CustomerVerifyModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\AuthModel;
use App\Notifications\WelcomeEmailNotification;
use Illuminate\Support\Facades\Storage;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail as Mail;
use Illuminate\Validation\Rules\File;
use Illuminate\Support\Str;
use Twilio\Rest\Verify;

class AuthController extends Controller
{    
    function __construct()
    {

    }
    
    function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:m_customers',
            'password' => 'required|string|min:8',
            'phone_number' => 'required|min:10|numeric|unique:m_customers,phone',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'status' => false,
                'data' => $validator->errors(),
            ], 200);
        }
        
        if ($request->email !== 'faizmsyam@gmail.com' && $request->email !== 'krazier.eights@gmail.com') {
            return response()->json([
                'code' => 401,
                'status' => false,
                'message' => 'Masih dalam tahap pengembangan, Silahkan kontak admin jika ingin membuat Undangan',
            ], 200);
        }

        $phone_number = $request->phone_number;
        if ($request->phone_number[0] === "0") {
            $phone_number = substr($phone_number, 1);
        }

        if ($phone_number[0] === "8") {
            // $phone_number = "62" . $phone_number;
            $phone_number = $phone_number;
        }
        $check_email = AuthModel::where('email', $request->email)->first();
        $check_phone = AuthModel::where('phone', $phone_number)->first();
        if ($check_email) {
            $check_register_social = CustomerSocialModel::where('customer_id', $check_email->id)->first();
            if ($check_register_social) {
                return response()->json([
                    'code' => 401,
                    'status' => false,
                    'message' => 'Your email is registered, Please login by ' . ucwords($check_register_social->service_name),
                ], 200);
            } else {
                return response()->json([
                    'code' => 401,
                    'status' => false,
                    'message' => 'Your email is registered',
                ], 200);
            }
        } else if ($check_phone) {
            return response()->json([
                'code' => 401,
                'status' => false,
                'message' => 'Your number phone is registered',
            ], 200);
        } else {
            $photo = $request->file('photo');
            $file_ext = NULL;
            $file_name = NULL;
            $file_path = NULL;
            if ($photo) {
                $file_ext = $photo->getClientOriginalExtension();
                $file_name = 'ava-'.time().'-'.sha1($request->name.$request->phone_number);
                $file_path = $photo->storeAs('images/customers', $file_name.'.'.$photo->getClientOriginalExtension(), 'public');
            }
    
            if ($file_path !== '') {
                $file_name = base64_encode($file_name);
            }
    
            $data_param = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone_code' => $request->phone_code,
                'phone_dial_code' => $request->phone_dial_code,
                'phone_domestic' => $request->phone_domestic,
                'phone_iso2' => $request->phone_iso2,
                'phone' => $phone_number,
                'photo' => $file_name,
                'photo_ext' => $file_ext,
                'known_source' => $request->known_from,
            ];
            $auth = AuthModel::create($data_param);
            if ($auth) {
                $param = [
                    'customer_id' => $auth->id,
                    'service_id' => random_int(1000000000000000, 9999999999999999),
                    'service_name' => 'email',
                ];
                CustomerSocialModel::create($param);

                // $this->whatsappNotification($auth->phone, $auth->name);
                // $auth->notify(new WelcomeEmailNotification($auth));
                $token = $auth->createToken('verify_token', ['*'], now()->addMinutes(10))->plainTextToken;
                // verifiy email send
                $token_verify = Str::random(64);
                $url_verify = url('api/email/verify/'.$auth->id.'?expires=') . strtotime(now()->addMinutes(10)) . '&ref=account_registration' . '&hash='.$token_verify . '&signature=' . sha1($auth->id . $token_verify);
                CustomerVerifyModel::create([
                    'customer_id' => $auth->id, 
                    'name' => 'verify_token',
                    'token' => $token_verify,
                    'expires_at' => now()->addMinutes(10),
                ]);

                Mail::to($request->email)->send(new VerifyEmail($request->name, $url_verify));

                return response()->json([
                    'code' => 200,
                    'status' => true,
                    'message' => 'You need to confirm your account. We have sent you an link activation, please check your email.',
                    'data' => $auth,
                    'token' => $token,
                    'token_type' => 'Bearer',
                ], 200);
            } else {
                return response()->json([
                    'code' => 400,
                    'status' => false,
                    'message' => 'Failed registration'
                ], 400);
            }
        }
    }

    private function whatsappNotification($recipient, $username)
    {
        $sid     = env("TWILIO_AUTH_SID");
        $token   = env("TWILIO_AUTH_TOKEN");
        $wa_from = env("TWILIO_WHATSAPP_FROM");
        $twilio  = new Client($sid, $token);
        $body = 'Hello '.$username.', Welcome to Wedding App.';
        return $twilio->messages->create("whatsapp:+$recipient", ["from" => "$wa_from", "body" => $body]);
    }

    function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|max:255|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'status' => false,
                'data' => $validator->errors(),
            ], 200);
        }

        if ($request->email !== 'faizmsyam@gmail.com' && $request->email !== 'krazier.eights@gmail.com') {
            return response()->json([
                'code' => 401,
                'status' => false,
                'message' => 'Masih dalam tahap pengembangan, Silahkan kontak admin jika ingin membuat Undangan',
            ], 200);
        }

        if (!Auth::guard('api')->attempt($request->only('email', 'password')))
        {
            return response()->json([
                'code' => 401,
                'status' => false, 
                'message' => 'The email and password you entered do not match',
            ], 200);
        }

        $auth = AuthModel::where('email', $request->email)->where('is_active', 1)->where('email_verified_at', '!=', NULL)->first();
        if ($auth) {
            $token = $auth->createToken('login_token', ['*'], now()->addHours(5))->plainTextToken;
            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Welcome ' . $auth->name,
                'token' => $token, 
                'token_type' => 'Bearer', 
                'is_verified' => $auth->is_active,
            ], 200);
        } else {
            $auth = AuthModel::where('email', $request->email)->where('is_active', 0)->where('email_verified_at', NULL)->first();
            if ($auth) {
                $token = $auth->createToken('verify_token', ['*'], now()->addMinutes(10))->plainTextToken;
                return response()->json([
                    'code' => 200,
                    'status' => true,
                    'message' => 'Your account has not been verified',
                    'token' => $token, 
                    'token_type' => 'Bearer', 
                    'is_verified' => $auth->is_active,
                ], 200);
            } else {
                return response()->json([
                    'code' => 404,
                    'status' => false,
                    'message' => 'Opss, account not found', 
                ], 404);
            }
        }
    }

    function profile()
    {
        $data = Auth::user();
        if ($data) {
            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Data Profile', 
                'data' => [
                    'id' => $data->id,
                    'name' => $data->name,
                    'email' => $data->email,
                    'phone_code' => $data->phone_code,
                    'phone_dial_code' => $data->phone_dial_code,
                    'phone_domestic' => $data->phone_domestic,
                    'phone_iso2' => $data->phone_iso2,
                    'phone' => $data->phone,
                    'address' => $data->address,
                    'province_id' => $data->province_id,
                    'city_id' => $data->city_id,
                    'district_id' => $data->district_id,
                    'subdistrict_id' => $data->subdistrict_id,
                    'photo' => !empty($data->photo) ? asset('storage/images/customers/'.base64_decode($data->photo).'.'.$data->photo_ext) : '',
                    'avatar' => !empty($data->avatar) ? $data->avatar : '',
                    'is_verified' => $data->is_active,
                ]
            ], 200);
        } else {
            return response()->json([
                'code' => 404,
                'status' => false,
                'message' => 'Data not found', 
            ], 404);
        }
    }


    function update_profile(Request $request)
    {
        $data = Auth::user();
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'phone_number' => 'required|min:10|max:13',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 200,
                'status' => false,
                'data' => $validator->errors(),
            ], 200);
        }

        $phone_number = $request->phone_number;
        if ($request->phone_number[0] === "0") {
            $phone_number = substr($phone_number, 1);
        }

        if ($phone_number[0] === "8") {
            // $phone_number = "62" . $phone_number;
            $phone_number = $phone_number;
        }

        $data_param = [
            'name' => $request->name,
            'phone_code' => $request->phone_code,
            'phone_dial_code' => $request->phone_dial_code,
            'phone_domestic' => $request->phone_domestic,
            'phone_iso2' => $request->phone_iso2,
            'phone' => $phone_number,
            'address' => $request->address,
            'province_id' => $request->province,
            'city_id' => $request->city,
            'district_id' => $request->district,
            'subdistrict_id' => $request->subdistrict,
        ];

        $data = AuthModel::where('id', $data->id)->update($data_param);
        if ($data) {
            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Success change data profile',
                'data' => [
                    'name' => $request->name,
                    'phone_code' => $request->phone_code,
                    'phone_dial_code' => $request->phone_dial_code,
                    'phone_domestic' => $request->phone_domestic,
                    'phone_iso2' => $request->phone_iso2,
                    'phone' => $phone_number,
                    'address' => $request->address,
                    'province_id' => $request->province,
                    'city_id' => $request->city,
                    'district_id' => $request->district,
                    'subdistrict_id' => $request->subdistrict,
                ],
            ], 200);
        } else {
            return response()->json([
                'code' => 400,
                'status' => false,
                'message' => 'Failed change data profile',
            ], 400);
        }
    }

    function update_avatar(Request $request)
    {
        $data = Auth::user();
        Storage::delete('public/images/customers/'.base64_decode($data->photo).'.'.$data->photo_ext);
        $path = public_path('storage/images/customers/');
        !is_dir($path) &&
            mkdir($path, 0777, true);

        $image_parts      = explode(";base64,", $request->photo);
        $image_type_aux   = explode("image/", $image_parts[0]);
        $image_type       = $image_type_aux[1];
        $image_base64     = base64_decode($image_parts[1]);
        $image_name       = base64_encode('ava-'.time().'-'.sha1($data->name));
        $image_full_path  = $path . base64_decode($image_name) . '.png';
        file_put_contents($image_full_path, $image_base64);

        $data_param = [
            'photo' => $image_name,
            'photo_ext' => $image_type,
        ];

        $data = AuthModel::where('id', $request->id)->update($data_param);
        if ($data) {
            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Success change your avatar',
                'data' => [
                    'photo' => asset('storage/images/customers/'.base64_decode($data_param['photo']).'.'.$data_param['photo_ext']),
                ]
            ], 200);
        } else {
            return response()->json([
                'code' => 400,
                'status' => false,
                'message' => 'Failed change your avatar',
            ], 200);
        }
    }

    function logout(AuthModel $authModel)
    {
        $authModel->tokens()->delete();
        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'You have logged out',
        ], 200);
    }

    function verify(Request $request, $id)
    {
        $expires = $request->expires;
        $hash = $request->hash;
        $ref = $request->ref;
        $signature = $request->signature;
        $valid_signature = sha1($id . $request->hash);
        $verify_user = CustomerVerifyModel::where('token', $hash)->where('customer_id', $id)->first();
        if ($ref == 'account_registration') {
            if ($valid_signature !== $signature) {
                return response()->json([
                    'code' => 400,
                    'status' => false,
                    'message' => 'The signature you sent is invalid',
                ], 200);    
            }
    
            if ($expires < strtotime(now()) ) {
                return response()->json([
                    'code' => 400,
                    'status' => false,
                    'message' => 'Invalid/expired url provided.',
                ], 200);
            }
            
            if (!is_null($verify_user)) {
                if ($verify_user->expires_at != null && (strtotime($verify_user->expires_at) < strtotime(now())) ) {
                    return response()->json([
                        'code' => 400,
                        'status' => false,
                        'message' => 'Invalid/expired url provided.',
                    ], 200);
                }

                $user = AuthModel::where('id', $id)->first();
                if (!$user->email_verified_at && ($user->is_active == 0)) {
                    AuthModel::where('id', $id)->where('email_verified_at', NULL)->update(['email_verified_at' => now(), 'is_active' => 1]);
                    $response = [
                        'code' => 200,
                        'status' => true,
                        'message' => 'Your email is verified. You can now login.',
                    ];
                    return redirect()->away(env('APP_URL_FRONTEND') . '/auth/login');
                } else {
                    $response = [
                        'code' => 200,
                        'status' => false,
                        'message' => 'Your email is already verified. You can now login.',
                    ];
                    return redirect()->away(env('APP_URL_FRONTEND') . '/auth/login');
                }
            } else {
                $response = [
                    'code' => 400,
                    'status' => false,
                    'message' => 'Sorry your email cannot be identified.',
                ];
                return response()->json($response, 200);
            }
        } else {
            return response()->json([
                'code' => 400,
                'status' => false,  
                'message' => 'Access denied',
            ], 200);
        }
    }

    function resend_verify(Request $request)
    {
        $ref = $request->ref;
        if ($ref == 'account_registration') {
            if ($request->id['is_verified'] == 0) {
                // verifiy email send
                $token_verify = Str::random(64);
                $url_verify = url('api/email/verify/'.$request->id['id'].'?expires=') . strtotime(now()->addMinutes(10)) . '&ref=account_registration' . '&hash='.$token_verify . '&signature=' . sha1($request->id['id'] . $token_verify);
                CustomerVerifyModel::create([
                    'customer_id' => $request->id['id'],
                    'name' => 'verify_token',
                    'token' => $token_verify,
                    'expires_at' => now()->addMinutes(10),
                ]);
                Mail::to($request->id['email'])->send(new VerifyEmail($request->id['name'], $url_verify));
    
                return response()->json([
                    'code' => 200,
                    'status' => true,
                    'message' => 'Email verification has been send to your mail. Please check email',
                ]);
            } else {
                return response()->json([
                    'code' => 200,
                    'status' => false,
                    'message' => 'Account has been verified',
                ]);
            }
        } else {
            return response()->json([
                'code' => 400,
                'status' => false,  
                'message' => 'Access denied',
            ], 200);
        }
    }
}
