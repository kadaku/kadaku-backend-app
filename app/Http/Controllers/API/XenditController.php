<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class XenditController extends Controller
{
    /** 
     * Read For The Documentation here:
     * https://packagist.org/packages/xendit/xendit-php
    */

    public function __construct()
    {
        
    }

    public function payment(Request $request)
    {
        $secret_key = 'Basic '.config('xendit.key_auth');
        $external_id = Str::random(10);
        $data_request = Http::withHeaders([
            'Authorization' => $secret_key
        ])->post('https://api.xendit.co/v2/invoices', [
            'external_id' => $external_id,
            'amount' => $request->amount,
            // 'payment_methods' => [ // allowed payment methods
            //     'BCA', 'QRIS'
            // ]
        ]);
        $response = $data_request->object();

        return response()->json([
            "status" => true,
            "message" => 'Test Xendit',
            "data" => $response
        ]);
    }

    public function handleCallbacks() {

    }
}
