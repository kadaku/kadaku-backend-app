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

	public function checkout_invoice_premium_account_activation(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'products.*.quantity' => 'required|numeric|gt:0',
			'products.*.category' => 'required|string|in:addon,package',
			'fees.*.type' => 'required|string|in:coupon discount,validation fee,unique code'
		]);

		if ($validator->fails()) :
			return response()->json([
				"status" => false,
				"message" => 'Error on validation payload',
				"data" => $validator->errors()
			], 406);
		endif;

		$pay_method = strtoupper($request->pay_method);
		// CHECK BANK ACCOUNTS / PAYMENT CHANNEL
		$payment_channel = $request->payment_channel;
		if (empty($payment_channel)) {
			return response()->json([
				"status" => false,
				"message" => 'Please choose a payment channel'
			], 409);
		}

		$payment_channel = DB::table('m_bank_accounts')->where('id', '=', $payment_channel)->first();
		// END CHECK BANK ACCOUNTS / PAYMENT CHANNEL

		$external_id = 'INV/KADAKU/' . date('YmdHis') . '/PREMIUM/' . Auth::user()->id . '/' . strtoupper(Str::random(10));
		$external_id_hash = sha1($external_id);

		if ($request->products < 1) :
			return response()->json([
				"status" => false,
				"message" => 'Please choose a product'
			], 409);
		endif;

		$amount = 0;

		// PRODUCTS
		$addons = array();
		$packages = array();

		$items = array();
		foreach ($request->products as $product) {
			switch ($product['category']) {
				case 'addon':
					$addon = DB::table('m_addons')->select('*')->where('id', $product['id'])->first();
					if ($addon) {
						$total = ($addon->price - ($addon->price * ($addon->discount / 100))) * $product['quantity'];

						// for payment detail
						$data_addon = $addon;
						$data_addon->total = $total;
						$addons[] = $data_addon;

						$items[] = [
							'name' => $addon->name,
							'price' => $total,
							'quantity' => $product['quantity'],
							'category' => $product['category'],
							'description' => $product['description'],
							'url' => $product['url']
						];
						$amount += $total;
					} else {
						return response()->json([
							"status" => false,
							"message" => 'Invalid addon or it doesn\'t satisfied the requirement',
							"data" => [
								"addon_id" => $product['id']
							]
						], 412);
					}
					break;
				case 'package':
					$package = DB::table('m_packages')->select('*')->where('id', $product['id'])->first();
					if ($package) {
						$total = ($package->price - ($package->price * ($package->discount / 100))) * $product['quantity'];

						// for payment detail
						$data_package = $package;
						$data_package->total = $total;
						$packages[] = $data_package;

						$items[] = [
							'name' => $package->name,
							'price' => $total,
							'quantity' => $product['quantity'],
							'category' => $product['category'],
							'description' => $product['description'],
							'url' => $product['url']
						];
						$amount += $total;
					} else {
						return response()->json([
							"status" => false,
							"message" => 'Invalid package or it doesn\'t satisfied the requirement',
							"data" => [
								"package_id" => $product['id']
							]
						], 412);
					}
					break;
				default:
					return response()->json([
						"status" => false,
						"message" => 'Invalid product category'
					], 412);
					break;
			}
		}

		// FEES
		$coupons = array();
		$fees = array();
		foreach ($request->fees as $fee) {
			switch ($fee['type']) {
				case 'coupon discount':
					$coupon = DB::table('m_coupons')->select('*')->where('id', $fee['id'])->where('minimum_amount', '<=', $amount)->first();
					if ($coupon) {
						$total = -$coupon->amount;

						// for payment detail
						$data_coupon = $coupon;
						$data_coupon->total = $total;
						$coupons[] = $data_coupon;

						$fees[] = [
							'type' => $fee['type'],
							'value' => $total
						];
						$amount += $total;
					} else {
						return response()->json([
							"status" => false,
							"message" => 'Invalid coupon or it doesn\'t satisfied the requirement',
							"data" => [
								"coupon_id" => $fee['id']
							]
						], 412);
					}
					break;
				case 'validation fee':
					$total = 5500;
					$fees[] = [
						'type' => $fee['type'],
						'value' => $total
					];
					$amount += $total;
					break;
				case 'unique code':
					$total = $fee['value'];
					$fees[] = [
						'type' => $fee['type'],
						'value' => $total
					];
					$amount += $total;
					break;
				default:
					return response()->json([
						"status" => false,
						"message" => 'Invalid fee category'
					], 412);
					break;
			}
		}

		if (!is_numeric($amount) || $amount <= 15000) :
			return response()->json([
				"status" => false,
				"message" => 'The amount is not allowed'
			], 403);
		endif;

		$customer = DB::table('m_customers')->select('id', 'name', 'email', 'email_verified_at', 'phone_code', 'phone_dial_code', 'phone', 'address')->where('id', '=', Auth::user()->id)->first();

		$payload = [
			"external_id" => $external_id,
			"payer_email" => Auth::user()->email,
			"items" => $items,
			"fees" => $fees,
			"description" => 'Invoice checkout from Kadaku for premium account subscription',
			"success_redirect_url" => $this->base_url . '/account/invoice/'.$external_id_hash.'?id=' . $external_id . '&status=success',
			"failure_redirect_url" => $this->base_url . '/account/invoice/'.$external_id_hash.'?id=' . $external_id . '&status=failure',
			"amount" => $amount,
			"paid_amount" => $amount,
			'payment_methods' => [ /* allowed payment methods */
			  // 'BCA', 'QRIS', 'BNI'
				$payment_channel->code, // FROM DATABASE
			]
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
						"message" => $response->error_code . ": " . $response->message . "\n" . $response->errors[0]->messages[0],
						"data" => $response->errors
					], 400);
				}

				$isExist = DB::table('t_payment_invoices')
					->where('invoice_id', $response->id)
					->where('external_id', $response->external_id)
					// ->where('payment_id', $payload['payment_id']) /*  Currently this object will only be returned when payment method that payer use are eWallets, PayLater, and QR code */
					->exists();

				if (!$isExist) :
					$params = [
						"invoice_id" => $response->id,
						"external_id" => $response->external_id,
						"user_id" => isset($response->user_id) ? $response->user_id : NULL,
						"customer_id" => isset(Auth::user()->id) ? Auth::user()->id : NULL,
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
						"items" => isset($response->items) ? json_encode($response->items) : NULL,
						"fees" => isset($response->fees) ? json_encode($response->fees) : NULL,
						"reminder_date" => isset($response->reminder_date) ? $response->reminder_date : Carbon::now()->addDay()->format('Y-m-d\TH:i:s.v\Z'),
						"payment_partners" => 'XENDIT',
						"payment_method_invoice" => isset($pay_method) ? $pay_method : NULL,
						"packages" => isset($packages) ? json_encode($packages) : NULL,
						"addons" => isset($addons) ? json_encode($addons) : NULL,
						"coupons" => isset($coupons) ? json_encode($coupons) : NULL,
						"customer" => isset($customer) ? json_encode($customer) : NULL,
					];

					$store = DB::table('t_payment_invoices')
						->insert($params);

					if ($store) :
						return response()->json([
							"status" => true,
							"message" => 'Success store invoice Xendit',
							"data" => [
								'id' => $params['invoice_id']
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

	public function invoice_callback(Request $request)
	{
		$payload = $request->all();
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
						if ($existedInvoice->paid_at != NULL || $existedInvoice->paid_amount != NULL) :
							return response()->json([
								"status" => true,
								"message" => 'Invoice Xendit based transaction has been successfully ' . $params['status'] . ' and nothing is updated'
							]);
						endif;

						$update = DB::table('t_payment_invoices')
							->where('invoice_id', $response->id)
							->where('external_id', $response->external_id)
							->update($params);

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
