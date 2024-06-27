<?php

namespace App\Http\Controllers;

use App\Models\InvoicesModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoicesController extends Controller
{
	function index()
	{
		$data['title'] = 'Invoices';
		return view('invoices.index', $data);
	}

	function list(Request $request)
	{
		if (empty($request->page)) {
			return response()->json([
				'code' => 400,
				'status' => false,
				'message' => 'Bad Request',
			], 200);
		}

		$param_search = [
			'keyword' => $request->keyword,
		];

		$limit = 10;
		$start = (((int) $request->page - 1) * $limit);

		$model = new InvoicesModel();
		$data = $model->list_data($start, $limit, $param_search);

		$data['page'] = (int) $request->page;
		$data['limit'] = $limit;

		if ($data['list']) {
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
				'data' => $data,
			];
			return response()->json($content, 200);
		}
	}

	function show($id)
	{
		if (empty($id)) {
			return response()->json([
				'code' => 400,
				'status' => false,
				'message' => 'Bad Request',
			], 200);
		}

		$data = InvoicesModel::find($id);
		if ($data) {
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
				'data' => $data,
			];
			return response()->json($content, 200);
		}
	}

	function update(Request $request)
	{
		if (empty($request->id)) {
			return response()->json([
				'code' => 400,
				'status' => false,
				'message' => 'Bad Request',
			], 200);
		}

		$existed_invoice = DB::table('t_payment_invoices')
			->where('id', '=', $request->id)
			->first();

		$days = 1;
		if ($existed_invoice->packages !== null) {
			$packages = json_decode($existed_invoice->packages);
			foreach ($packages as $package) {
				$days = $package->valid_days;
			}
		}

		$paid_at = Carbon::createFromFormat('Y-m-d H:i:s', now());
		$paid_at = $paid_at->format('Y-m-d\TH:i:s.v\Z');

		$set_paid = DB::table('t_payment_invoices')
		->where('id', $existed_invoice->id)
		->update([
			'status' => 'PAID',
			'paid_at' => $paid_at,
			'verified_by' => auth()->user()->id,
			'verified_at' => now(),
		]);

		$set_premium = DB::table('m_customers')
		->where('email', $existed_invoice->payer_email)
		->update([
			'is_trial' => 0,
			'is_premium' => 1,
			'start_at' => now(),
			'expired_at' => now()->addDays($days)
		]);

		if ($set_premium) {
			return response()->json([
				"code" => 200,
				"status" => true,
				"message" => 'Successfully to set premium account',
			]);
		} else {
			return response()->json([
				"code" => 400,
				"status" => false,
				"message" => 'Failed to set premium account',
			]);
		}
	}
}
