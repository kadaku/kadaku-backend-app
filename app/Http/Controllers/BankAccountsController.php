<?php

namespace App\Http\Controllers;

use App\Models\BankAccountsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BankAccountsController extends Controller
{
	protected $path;

	function __construct()
	{
		$this->path = 'images/banks/';
	}

	function index()
	{
		$data['title'] = 'Bank Accounts';
		return view('bank_accounts.index', $data);
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

		$model = new BankAccountsModel();
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

	function update(Request $request)
	{
		if (empty($request->id) && empty($request->status)) {
			return response()->json([
				'code' => 400,
				'status' => false,
				'message' => 'Bad Request',
			], 200);
		}

		$output = null;
		if ($request->status == 1) {
			$output = BankAccountsModel::where('id', $request->id)->update(['is_active' => 0]);
		} else if ($request->status == 0) {
			$output = BankAccountsModel::where('id', $request->id)->update(['is_active' => 1]);
		}

		if ($output) {
			$content = [
				'code' => 200,
				'status' => true,
				'message' => $this->message_update_success,
			];
			return response()->json($content, 200);
		} else {
			$content = [
				'code' => 400,
				'status' => false,
				'message' => $this->message_update_failed,
			];
			return response()->json($content, 200);
		}
	}

	function show($id)
	{
		$data = BankAccountsModel::find($id);
		if ($data) {
			$data->url_logo = NULL;
			if ($data->logo && Storage::disk('public')->exists($this->path . $data->logo)) {
				$data->url_logo = asset('storage/' . $this->path . $data->logo);
			}

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

	function store(Request $request)
	{
		// validation
		$validator = Validator::make($request->all(), [
			'name' => 'required|string|max:200',
			'code' => 'required|string',
			'logo' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
		]);

		$param_error['validate'] = false;
		if ($validator->fails()) {
			$data_error = $validator->errors();
			$param_error['validate'] = true;
			$param_error['data_error'] = $data_error;
			return response()->json($param_error, 200);
		}
		// end validations

		$slug = Str::of($request->name)->slug('-');
		$data = [
			'name' => $request->name,
			'code' => $request->code,
			'method' => $request->method,
			'account_name' => $request->account_name ?? $request->account_name,
			'account_number' => $request->account_number ?? $request->account_number,
			'is_invoice' => isset($_POST['is_invoice']) ? $request->is_invoice : 0,
			'is_digital_envelope' => isset($_POST['is_digital_envelope']) ? $request->is_digital_envelope : 0,
			'is_automatic_verification' => isset($_POST['is_automatic_verification']) ? $request->is_automatic_verification : 0,
			'is_manual_verification' => isset($_POST['is_manual_verification']) ? $request->is_manual_verification : 0,
		];

		// upload photo
		if ($request->file('logo')) {
			// remove the old avatar if it exists
			if ($request->logo_old && Storage::disk('public')->exists($this->path . $request->logo_old)) {
				Storage::disk('public')->delete($this->path . $request->logo_old);
			}
			$file = $request->file('logo');
			$file_ext = 'webp';
			$file_name = $slug;
			$file_name_fix = 'bank-' . $file_name . '-' . time() . '.' . $file_ext;
			$webp_logo = $this->convert_to_webp($file->getPathname());

			Storage::disk('public')->put($this->path . $file_name_fix, $webp_logo);
			$data['logo'] = $file_name_fix;
		}
		// end upload photo

		if (empty($request->id)) {
			$output = BankAccountsModel::create($data);
			if ($output) {
				$content = [
					'code' => 200,
					'status' => true,
					'message' => $this->message_add_success,
				];
				return response()->json($content, 200);
			} else {
				$content = [
					'code' => 400,
					'status' => false,
					'message' => $this->message_add_failed,
				];
				return response()->json($content, 200);
			}
		} else {
			$output = BankAccountsModel::where('id', $request->id)->update($data);
			if ($output) {
				$content = [
					'code' => 200,
					'status' => true,
					'message' => $this->message_update_success,
				];
				return response()->json($content, 200);
			} else {
				$content = [
					'code' => 400,
					'status' => false,
					'message' => $this->message_update_failed,
				];
				return response()->json($content, 200);
			}
		}
	}

	function destroy(int $id)
	{
		// remove the old avatar if it exists
		$find = BankAccountsModel::find($id, ['logo']);
		$output = BankAccountsModel::destroy($id);
		if ($output) {
			if ($find->logo && Storage::disk('public')->exists($this->path . $find->logo)) {
				Storage::disk('public')->delete($this->path . $find->logo);
			}
			$content = [
				'code' => 200,
				'status' => true,
				'message' => $this->message_destroy_success,
			];
			return response()->json($content, 200);
		} else {
			$content = [
				'code' => 400,
				'status' => false,
				'message' => $this->message_destroy_failed,
			];
			return response()->json($content, 200);
		}
	}
}
