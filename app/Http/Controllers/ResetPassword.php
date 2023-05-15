<?php

namespace App\Http\Controllers;

use App\Models\AuthModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

        $check_email = AuthModel::where('email', $request->email)->first();
        if ($check_email) {
            
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
}
