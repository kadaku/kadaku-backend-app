<?php

namespace App\Http\Controllers;

use App\Models\DevModel;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DevController extends Controller
{
  private $secret_key;
	private $base_url;

	public function __construct()
	{
		$this->secret_key = 'Basic ' . config('xendit.key_auth');
		$this->base_url = 'https://kadaku.id';
	}

	function coba() {
		$dateTime = Carbon::create('2024-06-22 12:32');
		$newDateTime = $dateTime->copy()->addHours(8);
		$formattedNewDateTime = $newDateTime->format('Y-m-d H:i');
		return response()->json([
			'initial_date_time' => $dateTime,
			'new_date_time' => $formattedNewDateTime,
		]);
	}
  
	function test_callback_xendit($id)
  {
    $payload['id'] = $id;
    $payload['external_id'] = 'INV/KADAKU/20240622003350/PREMIUM/1/RH0Y71CARA';
    try {
			$request_valid = Http::withHeaders([
				'Authorization' => $this->secret_key
			])->get('https://api.xendit.co/v2/invoices/' . $payload['id']);
			$response = $request_valid->object();

			if (!isset($response->external_id)) {
				return response()->json([
					"status" => true,
					"message" => 'Considered as a test, have a good day!'
				], 201);
			}

			// split the string by '-' delimiter
			$parts = explode('/', $response->external_id);
			// extract the Auth::user()->id from the parts array
			$userId = $parts[count($parts) - 2];
			// extract the context of payment from the parts array
			$context = $parts[count($parts) - 3];

			if ($response) :
				// Premium Account Payment Handler
				if ($context == 'PREMIUM') {
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

					$isExist = DB::table('t_payment_invoices')
						->where('invoice_id', $response->id)
						->where('external_id', $response->external_id)
						->exists();

					if ($isExist) {
						$existedInvoice = DB::table('t_payment_invoices')
							->where('invoice_id', $response->id)
							->where('external_id', $response->external_id)
							->first();
						// if ($existedInvoice->paid_at != NULL || $existedInvoice->paid_amount != NULL) :
						// 	return response()->json([
						// 		"status" => true,
						// 		"message" => 'Invoice Xendit based transaction has been successfully ' . $params['status'] . ' and nothing is updated'
						// 	]);
						// endif;

						$update = DB::table('t_payment_invoices')
							->where('invoice_id', $response->id)
							->where('external_id', $response->external_id)
							->update($params);

            $update = true;

						if ($update) :
							if ($params['status'] === "PAID" || $params['status'] === "SETTLED") :
								// transaction success
								$days = 1;
								if ($existedInvoice->packages !== null) {
                  $packages = json_decode($existedInvoice->packages);
                  foreach ($packages as $package) {
                    $days = $package->valid_days;
                  }
								}

								$setPremium = DB::table('m_customers')
									->where('id', $userId)
									->update([
										'is_trial' => 0,
										'is_premium' => 1,
										'start_at' => now(),
										'expired_at' => now()->addDays($days)
									]);

								if ($setPremium) {
									return response()->json([
										"status" => true,
										"message" => 'Invoice Xendit based transaction has been successfully ' . $params['status'] . ' and user\'s ' . $context . ' benefit is created'
									]);
								} else {
									return response()->json([
										"status" => true,
										"message" => 'Invoice Xendit based transaction has been successfully ' . $params['status'] . ' but user\'s ' . $context . ' benefit is not created yet'
									]);
								}
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
				} else if ($context == 'donate') {
					// handle donate logic
					return response()->json([
						"status" => true,
						"message" => $context . ' not available yet'
					]);
				} else {
					return response()->json([
						"status" => false,
						"message" => 'No expected context'
					], 417);
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
