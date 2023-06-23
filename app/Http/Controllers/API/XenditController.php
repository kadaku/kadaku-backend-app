<?php

namespace App\Http\Controllers\API;

use Exception;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class XenditController extends Controller
{
    /** 
     * Read For The Documentation here:
     * https://developers.xendit.co/api-reference
     */

    private $secret_key;
    private $base_url;

    public function __construct()
    {
        $this->secret_key = 'Basic ' . config('xendit.key_auth');
        $this->base_url = 'https://kadaku.id';
    }

    public function checkoutInvoice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'products.*.price' => 'required|numeric|gt:0',
            'products.*.quantity' => 'required|numeric|gt:0',
            'fees.*.type' => 'required',
            'fees.*.value' => 'required|numeric',
        ]);

        if ($validator->fails()) :
            return response()->json([
                "status" => false,
                "message" => 'Error on validation payload',
                "data" => $validator->errors()
            ], 406);
        endif;

        $external_id = 'invoice-kadaku-' . Str::random(10) . '-' . Auth::user()->id . '-' . time();

        if ($request->products < 1) :
            return response()->json([
                "status" => false,
                "message" => 'Please choose a product'
            ], 409);
        endif;

        $amount = 0;
        foreach ($request->products as $product) {
            $amount += $product['price'] * $product['quantity'];
        }
        foreach ($request->fees as $fee) {
            $amount += $fee['value'];
        }

        if (!is_numeric($amount) || $amount <= 10000) :
            return response()->json([
                "status" => false,
                "message" => 'The amount is not allowed'
            ], 403);
        endif;

        $payload = [
            "external_id" => $external_id,
            "payer_email" => Auth::user()->email,
            "items" => $request->products,
            "fees" => $request->fees,
            "description" => 'Invoice Checkout from Kadaku',
            "success_redirect_url" => $this->base_url . '/feedback?id=' . $external_id . '&status=success',
            "failure_redirect_url" => $this->base_url . '/feedback?id=' . $external_id . '&status=failure',
            "amount" => $amount,
            "paid_amount" => $amount,
            // 'payment_methods' => [ /* allowed payment methods */
            //     'BCA', 'QRIS', 'BNI'
            // ]
        ];

        try {
            $request_checkout = Http::withHeaders([
                'Authorization' => $this->secret_key
            ])->post('https://api.xendit.co/v2/invoices', $payload);
            $response = $request_checkout->object();

            if ($response) :
                if (isset($response->errors) && count($response->errors) > 0) {
                    return response()->json([
                        "status" => false,
                        "message" => $response->error_code . ": ". $response->message . "\n" . $response->errors[0]->messages[0],
                        "data" => $response->errors
                    ], 400);
                }

                $isExist = DB::table('t_payment_xendit_invoices')
                    ->where('invoice_id', $response->id)
                    ->where('external_id', $response->external_id)
                    // ->where('payment_id', $payload['payment_id']) /*  Currently this object will only be returned when payment method that payer use are eWallets, PayLater, and QR code */
                    ->exists();

                if (!$isExist) :
                    $params = [
                        "invoice_id" => $response->id,
                        "external_id" => $response->external_id,
                        "user_id" => isset($response->user_id) ? $response->user_id : NULL,
                        "status" => isset($response->status) ? $response->status : NULL,
                        "merchant_name" => isset($response->merchant_name) ? $response->merchant_name : NULL,
                        "amount" => isset($response->amount) ? $response->amount : NULL,
                        "payer_email" => isset($response->payer_email) ? $response->payer_email : NULL,
                        "description" => isset($response->description) ? $response->description : NULL,
                        "invoice_url" => isset($response->invoice_url) ? $response->invoice_url : NULL,
                        "success_redirect_url" => isset($response->success_redirect_url) ? $response->success_redirect_url : $this->base_url,
                        "failure_redirect_url" => isset($response->failure_redirect_url) ? $response->failure_redirect_url : $this->base_url,
                        "paid_amount" => isset($response->paid_amount) ? $response->paid_amount : NULL,
                        "created" => isset($response->created) ? $response->created : NULL,
                        "updated" => isset($response->updated) ? $response->updated : NULL,
                        "currency" => isset($response->currency) ? $response->currency : NULL,
                        "items" => isset($response->items) ? json_encode($response->items) : '[]',
                        "reminder_date" => isset($response->reminder_date) ? $response->reminder_date : Carbon::now()->addDay()->format('Y-m-d\TH:i:s.v\Z'),
                    ];

                    $store = DB::table('t_payment_xendit_invoices')
                        ->insert($params);

                    if ($store) :
                        return response()->json([
                            "status" => true,
                            "message" => 'Success store invoice Xendit',
                            "data" => [
                                'url' => $params['invoice_url']
                            ]
                        ]);
                    endif;
                    return response()->json([
                        "status" => false,
                        "message" => 'Failed to store invoice Xendit'
                    ], 405);
                endif;
                return response()->json([
                    "status" => false,
                    "message" => 'The duplication of invoice data is not allowed'
                ], 403);
            endif;
            return response()->json([
                "status" => false,
                "message" => 'Error when creating invoice from Xendit'
            ], 500);
        } catch (QueryException $e) {
            // Handle specific database query exceptions
            // Log the error, return an error response, etc.
            return response()->json([
                "status" => false,
                "message" => 'Database error: ' . $e->getMessage(),
                "line" => $e->getLine(),
                "file" => $e->getFile(),
            ], 500);
        } catch (Exception $e) {
            // Handle other generic exceptions
            // Log the error, return an error response, etc.
            return response()->json([
                "status" => false,
                "message" => 'An error occurred: ' . $e->getMessage(),
                "line" => $e->getLine(),
                "file" => $e->getFile(),
            ], 500);
        }
    }

    public function invoiceCallback(Request $request)
    {
        $payload = $request->all();

        try {
            $request_valid = Http::withHeaders([
                'Authorization' => $this->secret_key
            ])->get('https://api.xendit.co/v2/invoices/' . $payload['id']);
            $response = $request_valid->object();

            if ($response) :
                $params = [
                    "invoice_id" => isset($response->id) ? $response->id : NULL,
                    "external_id" => isset($response->external_id) ? $response->external_id : NULL,
                    "user_id" => isset($response->user_id) ? $response->user_id : NULL,
                    "is_high" => isset($payload['is_high']) ? $payload['is_high'] : false,
                    "status" => isset($response->status) ? $response->status : NULL,
                    "merchant_name" => isset($response->merchant_name) ? $response->merchant_name : NULL,
                    "amount" => isset($response->amount) ? $response->amount : NULL,
                    "payer_email" => isset($response->payer_email) ? $response->payer_email : NULL,
                    "expiry_date" => isset($response->expiry_date) ? $response->expiry_date : NULL,
                    "invoice_url" => isset($response->invoice_url) ? $response->invoice_url : NULL,
                    "description" => isset($payload['description']) ? $payload['description'] : NULL,
                    "paid_amount" => isset($response->paid_amount) ? $response->paid_amount : NULL,
                    "updated" => isset($response->updated) ? $response->updated : NULL,
                    "created" => isset($response->created) ? $response->created : NULL,
                    "currency" => isset($response->currency) ? $response->currency : NULL,
                    "paid_at" => isset($response->paid_at) ? $response->paid_at : NULL,
                    "payment_method" => isset($response->payment_method) ? $response->payment_method : NULL,
                    "payment_channel" => isset($response->payment_channel) ? $response->payment_channel : NULL,
                    "payment_destination" => isset($response->payment_destination) ? $response->payment_destination : NULL,
                    "payment_details" => isset($payload['payment_details']) ? json_encode($payload['payment_details']) : '{}', // Currently supporting eWallets, PayLater, and QR code only
                    "payment_id" => isset($payload['payment_id']) ? $payload['payment_id'] : 'default-payment-id-' . $payload['external_id'], // Currently supporting QRIS
                    "success_redirect_url" => isset($payload['success_redirect_url']) ? $payload['success_redirect_url'] : $this->base_url,
                    "failure_redirect_url" => isset($payload['failure_redirect_url']) ? $payload['failure_redirect_url'] : $this->base_url,
                    "credit_card_charge_id" => isset($payload['credit_card_charge_id']) ? $payload['credit_card_charge_id'] : NULL,
                    "items" => isset($response->items) ? json_encode($response->items) : '[]',
                    "fees" => isset($payload['fees']) ? json_encode($payload['fees']) : '[]',
                    "should_authenticate_credit_card" => isset($payload['should_authenticate_credit_card']) ? $payload['should_authenticate_credit_card'] : false,
                    "bank_code" => isset($response->bank_code) ? $response->bank_code : NULL,
                    "ewallet_type" => isset($payload['ewallet_type']) ? $payload['ewallet_type'] : NULL,
                    "on_demand_link" => isset($payload['on_demand_link']) ? $payload['on_demand_link'] : NULL,
                    "recurring_payment_id" => isset($payload['recurring_payment_id']) ? $payload['recurring_payment_id'] : NULL,
                ];

                $isExist = DB::table('t_payment_xendit_invoices')
                    ->where('invoice_id', $response->id)
                    ->where('external_id', $response->external_id)
                    ->exists();

                if ($isExist) {
                    $update = DB::table('t_payment_xendit_invoices')
                        ->where('invoice_id', $response->id)
                        ->where('external_id', $response->external_id)
                        ->update($params);

                    if ($update) :
                        if ($params['status'] === "PAID" || $params['status'] === "SETTLED") :
                            // Transaction success
                            return response()->json([
                                "status" => true,
                                "message" => 'Invoice Xendit based transaction has been successfully ' . $params['status']
                            ]);
                        endif;
                        return response()->json([
                            "status" => true,
                            "message" => 'Success update payment invoice Xendit'
                        ]);
                    endif;
                    return response()->json([
                        "status" => false,
                        "message" => 'Failed to update payment invoice Xendit'
                    ], 405);
                } else {
                    return response()->json([
                        "status" => false,
                        "message" => 'The invoice data was not found'
                    ], 404);
                }
            endif;
            return response()->json([
                "status" => false,
                "message" => 'Invalid invoice data'
            ], 403);
        } catch (QueryException $e) {
            return response()->json([
                "status" => false,
                "message" => 'Database error: ' . $e->getMessage(),
                "line" => $e->getLine(),
                "file" => $e->getFile(),
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                "status" => false,
                "message" => 'An error occurred: ' . $e->getMessage(),
                "line" => $e->getLine(),
                "file" => $e->getFile(),
            ], 500);
        }
    }
}
