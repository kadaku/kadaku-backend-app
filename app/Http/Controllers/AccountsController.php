<?php

namespace App\Http\Controllers;

use App\Models\AccountsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AccountsController extends Controller
{
	protected $path_photo;

	function __construct() {
		$this->path_photo = 'images/accounts/avatars/';
	}

	function index()
	{
		$user_groups = DB::table('c_user_groups')->where('is_active', 1)->get();
		return view('accounts.index', compact('user_groups'));
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

		$model = new AccountsModel();
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
			$output = AccountsModel::where('id', $request->id)->update(['is_active' => 0]);
		} else if ($request->status == 0) {
			$output = AccountsModel::where('id', $request->id)->update(['is_active' => 1]);
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
		$data = AccountsModel::find($id, ['id', 'user_group_id', 'name', 'email', 'phone', 'phone_code', 'phone_dial_code', 'photo', 'is_active']);
		if ($data) {
			if ($data->photo && Storage::disk('public')->exists($this->path_photo.$data->photo)) {
				$data->url_photo = asset('storage/'.$this->path_photo.$data->photo);
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
			'name' => 'required|string|max:255',
			'user_group_id' => 'required',
			'file_avatar' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
		]);
		
		$validator->sometimes('email', 'required|string|email:rfc,dns|unique:c_users,email', function ($input) {
			return ($input->id === null) ;
		});

		$param_error['validate'] = false;
		if ($validator->fails()) {
			$data_error = $validator->errors();
			$param_error['validate'] = true;
			$param_error['data_error'] = $data_error;
			return response()->json($param_error, 200);
		}
		// end validations

		$phone = NULL;
		if ($request->phone) {
			$phone = $request->phone;
			if ($request->phone[0] === "0") {
					$phone = substr($phone, 1);
			}
	
			if ($phone[0] === "8") {
					// $phone = "62" . $phone;
					$phone = $phone;
			}
		}

		$data = [
			'user_group_id' => $request->user_group_id,
			'name' => $request->name,
			'phone' => $phone,
			'phone_code' => $request->phone_code ?? $request->phone_code,
			'phone_dial_code' => $request->phone_dial_code ?? $request->phone_dial_code,
		];

		// upload photo
		if ($request->file('file_avatar')) {
			// remove the old avatar if it exists
			if ($request->file_avatar_old && Storage::disk('public')->exists($this->path_photo.$request->file_avatar_old)) {
				Storage::disk('public')->delete($this->path_photo.$request->file_avatar_old);
			}
			$file = $request->file('file_avatar');
			$file_ext = 'webp';
			$file_name = Str::of($request->name)->slug('-');
			$file_name_fix = $file_name.'.'.$file_ext;
			$webp_image = $this->convert_to_webp($file->getPathname());
			
			Storage::disk('public')->put($this->path_photo.$file_name_fix, $webp_image);
			$data['photo'] = $file_name_fix;
			$data['photo_ext'] = 'webp';
		}
		// end upload photo
		
		if (empty($request->id)) {
			$data['email'] = $request->email;
			$data['password'] = password_hash('12345', PASSWORD_DEFAULT);
			$output = AccountsModel::create($data);
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
			$output = AccountsModel::where('id', $request->id)->update($data);
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
		$find = AccountsModel::find($id, ['photo']);
		$output = AccountsModel::destroy($id);
		if ($output) {
			if ($find->photo && Storage::disk('public')->exists($this->path_photo.$find->photo)) {
				Storage::disk('public')->delete($this->path_photo.$find->photo);
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

	function reset_password(Request $request)
	{
		if (empty($request->id)) {
			return response()->json([
				'code' => 400,
				'status' => false,
				'message' => 'Bad Request',
			], 200);
		}

		$output = AccountsModel::where('id', $request->id)->update(['password' => password_hash('12345', PASSWORD_DEFAULT)]);
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
