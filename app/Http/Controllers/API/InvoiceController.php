<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\API\InvoiceModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
	function list(Request $request)
	{
		if (empty($request->page)) {
			return response()->json([
				'code' => 400,
				'status' => false,
				'message' => 'Bad Request',
			], 400);
		}

		$param_search = [
			'keyword' => $request->q,
		];

		$limit = 10;
		$start = (((int) $request->page - 1) * $limit);

		$model = new InvoiceModel();
		$data = $model->list_data($start, $limit, $param_search);

		$data['page'] = (int) $request->page;
		$data['limit'] = $limit;

		if (count($data['list']) > 0) {
			$content = [
				'code' => 200,
				'status' => true,
				'message' => $this->message_data_found,
				'data' => $data,
			];
			return response()->json($content, 200);
		} else {
			$content = [
				'code' => 404,
				'status' => false,
				'message' => $this->message_data_not_found,
				'data' => [
					'list' => [],
					'total' => 0,
				],
			];
			return response()->json($content, 200);
		}
	}

	function show(Request $request) 
	{
		if (empty($request->id)) {
			$content = [
				'code' => 400,
				'status' => false,
				'message' => $this->message_bad_request,
			];
			return response()->json($content, 400);
		}

		$external_id = $request->query('external_id');

		$query = DB::table("t_payment_invoices")->select(
			"external_id", 
			"invoice_url",
			"status",
			"amount",
			"payer_email",
			"payment_method_invoice",
			"items",
			"fees",
			"packages",
			"addons",
			"coupons",
			"customer",
			"reminder_date",
			"paid_at",
		);

		if ($external_id) {
			$query->where("customer_id", "=", Auth::user()->id)->where("external_id", "=", $external_id);
		} else {
			$query->where("customer_id", "=", Auth::user()->id)->where("invoice_id", "=", $request->id);
		}
		$data = $query->first();

		if ($data) {
			$data->payment_method_invoice = strtoupper($data->payment_method_invoice);
			
			if ($data->reminder_date) {
				$date_reminder = Carbon::createFromFormat('Y-m-d\TH:i:s.v\Z', $data->reminder_date);
				// convert to desired timezone and format
				$data->reminder_date = $date_reminder->setTimezone('Asia/Jakarta')->format('l, d F Y H:i');
				$data->reminder_date_origin = $date_reminder->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s');
			}
			
			if ($data->paid_at) {
				$date_paid = Carbon::createFromFormat('Y-m-d\TH:i:s.v\Z', $data->paid_at);
				// convert to desired timezone and format
				$data->paid_at = $date_paid->setTimezone('Asia/Jakarta')->format('l, d F Y H:i');
				$data->paid_at_origin = $date_paid->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s');
			}

			$data->items = json_decode($data->items);
			$data->fees = json_decode($data->fees);
			$data->packages = json_decode($data->packages);
			$data->addons = json_decode($data->addons);
			$data->coupons = json_decode($data->coupons);
			$data->customer = json_decode($data->customer);

			$content = [
				'code' => 200,
				'status' => true,
				'message' => $this->message_data_found,
				'data' => $data,
			];
			return response()->json($content, 200);
		} else {
			$content = [
				'code' => 404,
				'status' => false,
				'message' => $this->message_data_not_found,
				'data' => [],
			];
			return response()->json($content, 200);
		}	
	}
}
