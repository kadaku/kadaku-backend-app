<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvitationsController extends Controller
{
    function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'domain' => 'required|string|max:100',
            'category_id' => 'required|int',
            // 'theme_id' => 'required|int',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 200,
                'status' => false,
                'data' => $validator->errors(),
            ], 200);
        }
    }
}
