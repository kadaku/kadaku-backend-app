<?php

namespace App\Http\Controllers;

use App\Mail\ChangePasswordMail;
use App\Models\API\CustomerVerifyModel;
use App\Models\AuthModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ResetPassword extends Controller
{
    function send_mail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'status' => false,
                'data' => $validator->errors(),
            ], 200);
        }

        $auth = AuthModel::where('email', $request->email)->first();
        if ($auth) {
            // change password email send
            $token_change_password = Str::random(64);
            $url_change_password = env('APP_URL_FRONTEND') . '/password/change?email='.$auth->email.'&ref=change_password' . '&token='.$token_change_password . '&signature=' . sha1($auth->id . $token_change_password);
            CustomerVerifyModel::create([
                'customer_id' => $auth->id, 
                'name' => 'reset_token',
                'token' => $token_change_password,
                'expires_at' => now()->addMinutes(30),
            ]);

            $mail = Mail::to($auth->email)->send(new ChangePasswordMail($auth->name, $url_change_password));
            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Password reset successfully sent to your email...',
            ], 200);
        } else {
            return response()->json([
                'code' => 400,
                'status' => false,
                'data' => [
                    'email' => [
                        'The email entered is invalid.'
                    ]    
                ],
            ], 200);
        }
    }

    function validate_token(Request $request)
    {
        $email = $request->email;
        $token = $request->token;
        $signature = $request->signature;
        if (!empty($email) && !empty($token) && !empty($signature)) {
            $customer = DB::table('m_customers')->select('id')->where('email', $email)->first();
            if ($customer) {
                $valid_signature = sha1($customer->id . $token);
                if ($valid_signature === $signature) {
                    $verify = CustomerVerifyModel::where('customer_id', $customer->id)->where('name', 'reset_token')->where('token', $token)->first();
                    if ($verify) {
                        if (strtotime($verify->expires_at) < strtotime(now()) ) {
                            return response()->json([
                                'code' => 400,
                                'status' => false,
                                'message' => 'Invalid token, please reset again',
                            ], 200);
                        } else {
                            return response()->json([
                                'code' => 200,
                                'status' => true,
                                'message' => 'Token is valid',
                            ], 200);
                        }
                    } else {
                        return response()->json([
                            'code' => 400,
                            'status' => true,
                            'message' => 'Token is denied',
                        ], 200);
                    }
                } else {
                    return response()->json([
                        'code' => 400,
                        'status' => false,
                        'message' => 'The signature you sent is invalid',
                    ], 200);  
                }
            } else {
                return response()->json([
                    'code' => 404,
                    'status' => false,
                    'message' => 'Invalid email address',
                ], 200);  
            }
        } else {
            return response()->json([
                'code' => 400,
                'status' => false,
                'message' => 'Bad Request'
            ], 200);
        }
    }

    function change(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:100',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 400,
                'status' => false,
                'data' => $validator->errors(),
            ], 200);
        }

        $data_param = [
            'password' => Hash::make($request->password),
        ];
        
        $auth = AuthModel::where('email', $request->email)->update($data_param);
        if ($auth) {
            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Successfully changed password',
            ], 200);
        } else {
            return response()->json([
                'code' => 400,
                'status' => false,
                'message' => 'Failed to change password',
            ], 200);
        }
    }
}
