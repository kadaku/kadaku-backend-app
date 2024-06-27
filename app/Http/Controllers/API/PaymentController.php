<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
	private $base_url;

	public function __construct()
	{
		$this->base_url = 'https://kadaku.id';
	}

	public function checkout_invoice_premium_account_activation(Request $request)
	{
		if (!Auth::user()->id) {
			return response()->json([
				"status" => false,
				"message" => 'Account Unauthorized',
			], 401);
		}

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

		$payment_channel = DB::table('m_bank_accounts')->select('name','code','method','logo','account_name','account_number')->where('id', '=', $payment_channel)->first();
		if ($payment_channel) {
			if ($payment_channel->logo && Storage::disk('public')->exists('images/banks/' . $payment_channel->logo)) {
				$payment_channel->logo = asset('storage/images/banks/' . $payment_channel->logo);
			}
		}
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

		if (!is_numeric($amount) || $amount <= 10000) :
			return response()->json([
				"status" => false,
				"message" => 'The amount is not allowed'
			], 403);
		endif;

		$customer = DB::table('m_customers')->select('id', 'name', 'email', 'email_verified_at', 'phone_code', 'phone_dial_code', 'phone', 'address')->where('id', '=', Auth::user()->id)->first();

		$date_reminder = Carbon::create(now());
		$date_reminder = $date_reminder->copy()->addHours(8)->format('Y-m-d\TH:i:s.v\Z');
		
		$created = Carbon::createFromFormat('Y-m-d H:i:s', now());
		$created = $created->format('Y-m-d\TH:i:s.v\Z');

		$invoice_url = "https://api.whatsapp.com/send/?phone=6285966622963&text=Halo Admin%0ASaya Ingin Konfirmasi Pembayaran%0A%2A".$external_id."%2A%0ANominal+%2ARp+".number_format($amount, 0, '', ',')."%2A%0A%0ALampirkan Bukti Transfer&type=phone_number&app_absent=0";

		$params = [
			"invoice_id" => $external_id_hash,
			"external_id" => $external_id,
			"user_id" => sha1(Auth::user()->id),
			"customer_id" => Auth::user()->id,
			"status" => 'UNPAID',
			"merchant_name" => 'Kadaku',
			"amount" => $amount,
			"payer_email" => Auth::user()->email,
			"description" => 'Invoice checkout from Kadaku for premium account subscription',
			"created" => $created,
			"updated" => $created,
			"currency" => 'IDR',
			"invoice_url" => $invoice_url,
			"success_redirect_url" => $this->base_url . '/account/invoice/'.$external_id_hash.'?id=' . $external_id . '&status=success',
			"failure_redirect_url" => $this->base_url . '/account/invoice/'.$external_id_hash.'?id=' . $external_id . '&status=failure',
			"paid_amount" => $amount,
			"items" => json_encode($items),
			"fees" => json_encode($fees),
			"reminder_date" => $date_reminder,
			"payment_partners" => 'MANUAL',
			"payment_method_invoice" => isset($pay_method) ? $pay_method : NULL,
			"payment_method" => $payment_channel->method,
			"payment_channel" => $payment_channel->code,
			"packages" => isset($packages) ? json_encode($packages) : NULL,
			"addons" => isset($addons) ? json_encode($addons) : NULL,
			"coupons" => isset($coupons) ? json_encode($coupons) : NULL,
			"customer" => isset($customer) ? json_encode($customer) : NULL,
			"bank_accounts" => json_encode($payment_channel)
		];

		$store = DB::table('t_payment_invoices')
			->insert($params);

		if ($store) :
			return response()->json([
				"status" => true,
				"message" => 'Success store invoice',
				"data" => [
					'id' => $params['invoice_id']
				]
			], 200);
		endif;
		return response()->json([
			"status" => false,
			"message" => 'Failed to store invoice'
		], 405);
	}
}
