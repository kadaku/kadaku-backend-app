<?php

namespace App\Http\Controllers;

use App\Models\LayoutsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LayoutsController extends Controller
{
	protected $path;

	function __construct()
	{
		$this->path = 'images/layouts/';
	}

	function index()
	{
		$data['title'] = 'Layouts';
		$data['categories_layouts'] = DB::table('m_categories_layouts')->where('is_active', 1)->get();
		return view('invitation_layouts.index', $data);
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

		$model = new LayoutsModel();
		$data = $model->list_data($start, $limit, $param_search);

		$data['page'] = (int) $request->page;
		$data['limit'] = $limit;

		if ($data['list']) {
			foreach ($data['list'] as $i => $value) {
				$data['list'][$i]->url_image = NULL;
				if ($value->image && Storage::disk('public')->exists($this->path . $value->image)) {
					$data['list'][$i]->url_image = asset('storage/' . $this->path . $value->image);
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
			$output = LayoutsModel::where('id', $request->id)->update(['is_active' => 0]);
		} else if ($request->status == 0) {
			$output = LayoutsModel::where('id', $request->id)->update(['is_active' => 1]);
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
		$data = LayoutsModel::find($id);
		if ($data) {
			$data->url_image = NULL;
			if ($data->image && Storage::disk('public')->exists($this->path . $data->image)) {
				$data->url_image = asset('storage/' . $this->path . $data->image);
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
			'category_layout_id' => 'required|numeric',
			'title' => 'required|string|max:200',
			'icon' => 'required|string',
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

		if (empty($request->body)) {
			$content = [
				'code' => 400,
				'status' => false,
				'message' => 'The content field is required.',
			];
			return response()->json($content, 200);
		}

		$slug = Str::of($request->title)->slug('-');
		$data = [
			'category_layout_id' => $request->category_layout_id,
			'title' => $request->title,
			'icon' => $request->icon,
			'is_premium' => isset($_POST['is_premium']) ? $request->is_premium : 0,
			'body' => $request->body,
			'order' => isset($_POST['order']) ? $request->order : 1,
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
			$file_name_fix = $file_name . '-' . date('YmdHis') .  '.' . $file_ext;
			$webp_image = $this->convert_to_webp($file->getPathname());

			Storage::disk('public')->put($this->path . $file_name_fix, $webp_image);
			$data['image'] = $file_name_fix;
		}
		// end upload photo

		if (empty($request->id)) {
			$output = LayoutsModel::insertGetId($data);
			if ($output) {
				$content = [
					'code' => 200,
					'status' => true,
					'message' => $this->message_add_success,
					'data' => [
						'id' => $output,
					],
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
			$output = LayoutsModel::where('id', $request->id)->update($data);
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
		$find = LayoutsModel::find($id, ['image']);
		$output = LayoutsModel::destroy($id);
		if ($output) {
			if ($find->image && Storage::disk('public')->exists($this->path . $find->image)) {
				Storage::disk('public')->delete($this->path . $find->image);
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
