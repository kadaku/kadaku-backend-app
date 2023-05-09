<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\AuthModel;
use App\Notifications\WelcomeEmailNotification;
use Illuminate\Support\Facades\Storage;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\File;


class AuthController extends Controller
{
    
    function register(Request $request)
    {
        $check = AuthModel::where('email', $request->email)->first();
        if ($check) {
            return response()->json([
                'status' => true,
                'message' => 'Your email is registered',
            ], Response::HTTP_OK);
        } else {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:m_customers',
                'password' => 'required|string|min:8',
                'phone_number' => 'required|min:10|max:13',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'code' => 400,
                    'status' => false,
                    'data' => $validator->errors(),
                ], Response::HTTP_OK);
            }
    
            $photo = $request->file('photo');
            $file_ext = NULL;
            $file_name = NULL;
            $file_path = NULL;
            if ($photo) {
                $file_ext = $photo->getClientOriginalExtension();
                $file_name = 'ava-'.time().'-'.sha1($request->name.$request->phone_number);
                $file_path = $photo->storeAs('images/customers', $file_name.'.'.$photo->getClientOriginalExtension(), 'public');
            }
    
            $phone_number = $request->phone_number;
            if ($request->phone_number[0] === "0") {
                $phone_number = substr($phone_number, 1);
            }
    
            if ($phone_number[0] === "8") {
                $phone_number = "62" . $phone_number;
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
                'phone' => $phone_number,
                'photo' => $file_name,
                'photo_ext' => $file_ext,
            ];
            $auth = AuthModel::create($data_param);
            if ($auth) {
                // $this->whatsappNotification($auth->phone, $auth->name);
                // $auth->notify(new WelcomeEmailNotification($auth));
                $token = $auth->createToken('auth_token')->plainTextToken;
        
                return response()->json([
                    'code' => 200,
                    'status' => true,
                    'data' => $auth,
                    'token' => $token,
                    'token_type' => 'Bearer',
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'code' => 400,
                    'status' => false,
                    'message' => 'Failed Registration'
                ], Response::HTTP_BAD_GATEWAY);
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
            ], Response::HTTP_OK);
        }

        if (!Auth::guard('api')->attempt($request->only('email', 'password')))
        {
            return response()->json([
                'code' => 401,
                'status' => false, 
                'message' => 'The email and password you entered do not match',
            ], Response::HTTP_OK);
        }

        $auth = AuthModel::where('email', $request['email'])->where('is_active', 1)->first();
        if ($auth) {
            $token = $auth->createToken('auth_token')->plainTextToken;
            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Welcome ' . $auth->name,
                'token' => $token, 
                'is_verified' => 1,
                'token_type' => 'Bearer', 
            ], Response::HTTP_OK);
        } else {
            $auth = AuthModel::where('email', $request['email'])->where('is_active', 0)->first();
            if ($auth) {
                return response()->json([
                    'code' => 200,
                    'status' => true,
                    'is_verified' => 0,
                    'message' => 'Your account has not been verified', 
                ], Response::HTTP_OK);
            } else {
                return response()->json([
                    'code' => 404,
                    'status' => false,
                    'message' => 'Opss, Account not found', 
                ], Response::HTTP_NOT_FOUND);
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
                    'phone' => $data->phone,
                    'photo' => asset('storage/images/customers/'.base64_decode($data->photo).'.'.$data->photo_ext),
                ]
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'code' => 404,
                'status' => false,
                'message' => 'Data not found', 
            ], Response::HTTP_NOT_FOUND);
        }
    }


    function update_profile(Request $request, $id)
    {
        $data = Auth::user();
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'password' => 'string|min:8',
            'phone_number' => 'required|min:10|max:13',
            'photo' => File::image()->max(2024),
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
        }

        $photo = $request->file('photo');
        $file_ext = '';
        $file_path = '';
        if ($photo) {
            Storage::delete('public/images/customers/'.base64_decode($data->photo).'.'.$data->photo_ext);
            $file_ext = $photo->getClientOriginalExtension();
            $file_name = 'ava-'.time().'-'.sha1($request->name.$request->phone_number);
            $file_path = $photo->storeAs('images/customers', $file_name.'.'.$photo->getClientOriginalExtension(), 'public');
        }

        if ($file_path !== '') {
            $file_name = base64_encode($file_name);
        }

        $phone_number = $request->phone_number;
        if ($request->phone_number[0] === "0") {
            $phone_number = substr($phone_number, 1);
        }

        if ($phone_number[0] === "8") {
            $phone_number = "62" . $phone_number;
        }

        if ($file_path !== '') {
            $file_name = base64_encode($file_name);
        }

        $data_param = [
            'name' => $request->name,
            'phone_code' => $request->phone_code,
            'phone_dial_code' => $request->phone_dial_code,
            'phone' => $phone_number,
            'photo' => $file_path ? $file_name : $data->photo,
            'photo_ext' => $file_path ? $file_ext : $data->photo_ext,
        ];

        $data = AuthModel::where('id', $id)->update($data_param);
        if ($data) {
            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Success change data profile',
                'data' => [
                    'id' => $id,
                    'name' => $data_param['name'],
                    'photo' => asset('storage/images/customers/'.base64_decode($data_param['photo']).'.'.$data_param['photo_ext']),
                ],
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'code' => 400,
                'status' => false,
                'message' => 'Failed change data profile',
            ], Response::HTTP_BAD_GATEWAY);
        }
    }

    function logout(AuthModel $authModel)
    {
        $authModel->tokens()->delete();
        return response()->json([
            'code' => 200,
            'status' => true,
            'message' => 'You have logged out',
        ], Response::HTTP_OK);
    }
}
