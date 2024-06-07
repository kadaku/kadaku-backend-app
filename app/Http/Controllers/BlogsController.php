<?php

namespace App\Http\Controllers;

use App\Models\BlogsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BlogsController extends Controller
{
	protected $path;

	function __construct()
	{
		$this->path = 'images/blogs/';
	}

	function index()
	{
		$data['title'] = 'Blogs';
		return view('blogs.index', $data);
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

		$model = new BlogsModel();
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

	function update_status(Request $request)
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
			$output = BlogsModel::where('id', $request->id)->update(['is_active' => 0]);
		} else if ($request->status == 0) {
			$output = BlogsModel::where('id', $request->id)->update(['is_active' => 1]);
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

	function update_publish(Request $request)
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
			$output = BlogsModel::where('id', $request->id)->update(['is_publish' => 0]);
		} else if ($request->status == 0) {
			$output = BlogsModel::where('id', $request->id)->update(['is_publish' => 1]);
			$output = BlogsModel::where('id', $request->id)->update(['published_date' => date('y-m-d H:i:s')]);
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
		$data = BlogsModel::find($id);
		if ($data) {
			$data->url_featured_image = NULL;
			if ($data->featured_image && Storage::disk('public')->exists($this->path . $data->featured_image)) {
				$data->url_featured_image = asset('storage/' . $this->path . $data->featured_image);
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
			'intro' => 'required|string|max:160',
			// 'written_by' => 'required|string|max:200',
			'featured_image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
		]);

		$param_error['validate'] = false;
		if ($validator->fails()) {
			$data_error = $validator->errors();
			$param_error['validate'] = true;
			$param_error['data_error'] = $data_error;
			return response()->json($param_error, 200);
		}
		// end validations

		if (empty($request->content)) {
			$content = [
				'code' => 400,
				'status' => false,
				'message' => 'The content field is required.',
			];
			return response()->json($content, 200);
		}

		$slug = Str::of($request->name)->slug('-');
		$data = [
			'name' => $request->name,
			'slug' => $slug,
			'topic' => $request->topic ?? $request->topic,
			'intro' => $request->intro,
			'content' => $request->content ?? $request->content,
			'source' => $request->source ?? $request->source,
			// 'written_by' => !empty($request->written_by) ? $request->written_by : auth()->user()->name,
			'written_by' => auth()->user()->name,
			'tags' => $request->tags ?? $request->tags,
		];

		// upload photo
		if ($request->file('featured_image')) {
			// remove the old avatar if it exists
			if ($request->featured_image_old && Storage::disk('public')->exists($this->path.$request->featured_image_old)) {
				Storage::disk('public')->delete($this->path.$request->featured_image_old);
			}
			$file = $request->file('featured_image');
			$file_ext = 'webp';
			$file_name = $slug;
			$file_name_fix = $file_name.'.'.$file_ext;
			$webp_image = $this->convert_to_webp($file->getPathname());
			
			Storage::disk('public')->put($this->path.$file_name_fix, $webp_image);
			$data['featured_image'] = $file_name_fix;
		}
		// end upload photo
		
		if (empty($request->id)) {
			$output = BlogsModel::create($data);
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
			$output = BlogsModel::where('id', $request->id)->update($data);
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
		$find = BlogsModel::find($id, ['featured_image']);
		$output = BlogsModel::destroy($id);
		if ($output) {
			if ($find->featured_image && Storage::disk('public')->exists($this->path.$find->featured_image)) {
				Storage::disk('public')->delete($this->path.$find->featured_image);
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
