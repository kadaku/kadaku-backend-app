<?php

namespace App\Http\Controllers;

use App\Models\CouponsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CouponsController extends Controller
{
	protected $path_thumbnails;

	function __construct()
	{
		$this->path_thumbnails = 'images/coupons/thumbnails/';
	}

	function index()
	{
		$data['title'] = 'Coupons';
		return view('coupons.index', $data);
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

		$model = new CouponsModel();
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
			$output = CouponsModel::where('id', $request->id)->update(['is_active' => 0]);
		} else if ($request->status == 0) {
			$output = CouponsModel::where('id', $request->id)->update(['is_active' => 1]);
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
		$data = CouponsModel::find($id);
		if ($data) {
			$data->url_thumbnail = NULL;
			if ($data->thumbnail && Storage::disk('public')->exists($this->path_thumbnails . $data->thumbnail)) {
				$data->url_thumbnail = asset('storage/' . $this->path_thumbnails . $data->thumbnail);
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
			'description' => 'required|string',
			'amount' => 'required|numeric',
			'minimum_amount' => 'required|numeric',
			'periode_start' => 'required|date',
			'periode_end' => 'required|date',
			'file_thumbnail' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
		]);

		$validator->sometimes('name', 'required|string|max:255|unique:m_coupons', function ($input) {
			return ($input->id === null) ;
		});
		
		$validator->sometimes('code', 'required|string|max:30|unique:m_coupons', function ($input) {
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

		$data = [
			'name' => $request->name,
			'description' => $request->description,
			'code' => $request->code,
			'periode_start' => $request->periode_start,
			'periode_end' => $request->periode_end,
			'amount' => currency_to_number($request->amount),
			'minimum_amount' => currency_to_number($request->minimum_amount),
			'is_private' => $request->is_private ? 1 : 0,
			'is_showing' => $request->is_showing ? 1 : 0,
		];

		// upload photo
		if ($request->file('file_thumbnail')) {
			// remove the old avatar if it exists
			if ($request->file_thumbnail_old && Storage::disk('public')->exists($this->path_thumbnails . $request->file_thumbnail_old)) {
				Storage::disk('public')->delete($this->path_thumbnails . $request->file_thumbnail_old);
			}
			$file = $request->file('file_thumbnail');
			$file_ext = 'webp';
			$file_name = Str::of($request->name)->slug('-');
			$file_name_fix = $file_name . '.' . $file_ext;
			$webp_image = $this->convert_to_webp($file->getPathname());

			Storage::disk('public')->put($this->path_thumbnails . $file_name_fix, $webp_image);
			$data['thumbnail'] = $file_name_fix;
		}
		// end upload photo

		if (empty($request->id)) {
			$data['user_id'] = auth()->user()->id;
			$output = CouponsModel::create($data);
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
			$output = CouponsModel::where('id', $request->id)->update($data);
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
		$find = CouponsModel::find($id, ['thumbnail']);
		$output = CouponsModel::destroy($id);
		if ($output) {
			if ($find->thumbnail && Storage::disk('public')->exists($this->path_thumbnails . $find->thumbnail)) {
				Storage::disk('public')->delete($this->path_thumbnails . $find->thumbnail);
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
