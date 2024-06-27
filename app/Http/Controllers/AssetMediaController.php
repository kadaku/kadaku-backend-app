<?php

namespace App\Http\Controllers;

use App\Models\AssetMediaModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AssetMediaController extends Controller
{
	protected $path;

	function __construct()
	{
		$this->path = 'images/media/';
	}

	function index()
	{
		$data['title'] = 'Asset Media';
		$data['categories'] = AssetMediaModel::list_categories();
		return view('asset_media.index', $data);
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

		$model = new AssetMediaModel();
		$data = $model->list_data($start, $limit, $param_search);

		$data['page'] = (int) $request->page;
		$data['limit'] = $limit;

		if ($data['list']) {
			foreach ($data['list'] as $i => $value) {
				$data['list'][$i]->url_file = NULL;
				if ($value->file && Storage::disk('public')->exists($this->path . $value->file)) {
					$data['list'][$i]->url_file = asset('storage/' . $this->path . $value->file);
				}
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
			$output = AssetMediaModel::where('id', $request->id)->update(['is_active' => 0]);
		} else if ($request->status == 0) {
			$output = AssetMediaModel::where('id', $request->id)->update(['is_active' => 1]);
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
		$data = AssetMediaModel::find($id);
		if ($data) {
			$data->url_file = NULL;
			if ($data->file && Storage::disk('public')->exists($this->path . $data->file)) {
				$data->url_file = asset('storage/' . $this->path . $data->file);
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
			'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
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
			'description' => $request->description ? $request->description : NULL,
			'keyword' => $request->keyword,
		];

		$directory_path = public_path($this->path);
		if (!Storage::disk('public')->exists($directory_path)) {
			Storage::disk('public')->makeDirectory($directory_path, 777, true);
		}

		// upload photo
		if ($request->file('image')) {
			// remove the old avatar if it exists
			if ($request->image_old && Storage::disk('public')->exists($this->path . $request->image_old)) {
				Storage::disk('public')->delete($this->path . $request->image_old);
			}
			$file = $request->file('image');
			$file_ext = 'webp';
			$file_name = $slug;
			$file_name_fix = 'asset-' . Str::random('5') . '-' . time() . '.' . $file_ext;
			$webp_image = $this->convert_to_webp($file->getPathname());

			Storage::disk('public')->put($this->path . $file_name_fix, $webp_image);
			$data['file'] = $file_name_fix;
		}
		// end upload photo

		if (empty($request->id)) {
			$output = AssetMediaModel::create($data);
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
			$output = AssetMediaModel::where('id', $request->id)->update($data);
			if ($output) {
				$content = [
					'code' => 200,
					'status' => true,
					'message' => $this->message_update_success,
					'data' => [
						'id' => $request->id,
					]
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
		$find = AssetMediaModel::find($id, ['file']);
		$output = AssetMediaModel::destroy($id);
		if ($output) {
			if ($find->fiile && Storage::disk('public')->exists($this->path . $find->fiile)) {
				Storage::disk('public')->delete($this->path . $find->fiile);
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
