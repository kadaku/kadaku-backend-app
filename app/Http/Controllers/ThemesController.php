<?php

namespace App\Http\Controllers;

use App\Models\ThemesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ThemesController extends Controller
{
	protected $path_thumbnail;
	protected $path_background;

	function __construct()
	{
		$this->path_thumbnail = 'images/themes/thumbnails/';
		$this->path_background = 'images/themes/backgrounds/';
	}

	function index()
	{
		$data['title'] = 'Themes';
		$data['categories_themes'] = DB::table('m_categories')->where('is_active', 1)->get();
		$data['themes_type'] = DB::table('m_themes_type')->where('is_active', 1)->get();
		return view('themes.index', $data);
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

		$model = new ThemesModel();
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
			$output = ThemesModel::where('id', $request->id)->update(['is_active' => 0]);
		} else if ($request->status == 0) {
			$output = ThemesModel::where('id', $request->id)->update(['is_active' => 1]);
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
		$data = ThemesModel::find($id);
		if ($data) {
			$data->url_background = NULL;
			if ($data->background && Storage::disk('public')->exists($this->path_background . $data->background)) {
				$data->url_background = asset('storage/' . $this->path_background . $data->background);
			}

			$data->url_thumbnail = NULL;
			if ($data->thumbnail && Storage::disk('public')->exists($this->path_thumbnail . $data->thumbnail)) {
				$data->url_thumbnail = asset('storage/' . $this->path_thumbnail . $data->thumbnail);
			}

			$data->url_thumbnail_xs = NULL;
			if ($data->thumbnail_xs && Storage::disk('public')->exists($this->path_thumbnail . $data->thumbnail_xs)) {
				$data->url_thumbnail_xs = asset('storage/' . $this->path_thumbnail . $data->thumbnail_xs);
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
			'category_id' => 'required|numeric',
			'type_id' => 'required|numeric',
			'name' => 'required|string|max:200',
			'layout' => 'required|string',
			'font' => 'required|string',
			'color_primary' => 'required|string',
			'color_secondary' => 'required|string',
			'color_tertiary' => 'required|string',
			'color_quaternary' => 'required|string',
			'background' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
			'thumbnail' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
			'thumbnail_xs' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
		]);

		$param_error['validate'] = false;
		if ($validator->fails()) {
			$data_error = $validator->errors();
			$param_error['validate'] = true;
			$param_error['data_error'] = $data_error;
			return response()->json($param_error, 200);
		}
		// end validations

		// styles
		$styles = [
			'font' => $request->font_heading ? $request->font_heading : '',
			'font_body' => $request->font_body ? $request->font_body : '',
			'colors' => [
				'primary' => $request->color_primary ? $request->color_primary : '',
				'secondary' => $request->color_secondary ? $request->color_secondary : '',
				'tertiary' => $request->color_tertiary ? $request->color_tertiary : '',
				'quaternary' => $request->color_quaternary ? $request->color_quaternary : '',
			],
		];
		$styles = json_encode($styles);
		// end styles

		$slug = Str::of($request->name)->slug('-');
		$data = [
			'category_id' => $request->category_id,
			'type_id' => $request->type_id,
			'name' => $request->name,
			'slug' => $slug,
			'description' => $request->description ?? $request->description,
			'styles' => $styles,
			'layout' => $request->layout,
			'price' => !empty($request->price) ? currency_to_number($request->price) : 0,
			'discount' => !empty($request->discount) ? $request->discount : 0,
			'is_premium' => isset($_POST['is_premium']) ? $request->is_premium : 0,
			'version' => isset($_POST['version']) ? $request->version : 1,
		];

		// upload background
		if ($request->file('background')) {
			// remove the old avatar if it exists
			if ($request->background_old && Storage::disk('public')->exists($this->path_background . $request->background_old)) {
				Storage::disk('public')->delete($this->path_background . $request->background_old);
			}
			$file = $request->file('background');
			$file_ext = 'webp';
			$file_name = $slug;
			$file_name_fix = 'background-theme-' . $file_name . '.' . $file_ext;
			$webp_background = $this->convert_to_webp($file->getPathname());

			Storage::disk('public')->put($this->path_background . $file_name_fix, $webp_background);
			$data['background'] = $file_name_fix;
		}
		// end upload background

		// upload thumbnail
		if ($request->file('thumbnail')) {
			// remove the old avatar if it exists
			if ($request->thumbnail_old && Storage::disk('public')->exists($this->path_thumbnail . $request->thumbnail_old)) {
				Storage::disk('public')->delete($this->path_thumbnail . $request->thumbnail_old);
			}
			$file = $request->file('thumbnail');
			$file_ext = 'webp';
			$file_name = $slug;
			$file_name_fix = 'thumbnail-theme-' . $file_name . '.' . $file_ext;
			$webp_thumbnail = $this->convert_to_webp($file->getPathname());

			Storage::disk('public')->put($this->path_thumbnail . $file_name_fix, $webp_thumbnail);
			$data['thumbnail'] = $file_name_fix;
		}
		// end upload thumbnail

		// upload thumbnail xs
		if ($request->file('thumbnail_xs')) {
			// remove the old avatar if it exists
			if ($request->thumbnail_xs_old && Storage::disk('public')->exists($this->path_thumbnail . $request->thumbnail_xs_old)) {
				Storage::disk('public')->delete($this->path_thumbnail . $request->thumbnail_xs_old);
			}
			$file = $request->file('thumbnail_xs');
			$file_ext = 'webp';
			$file_name = $slug;
			$file_name_fix = 'thumbnail_xs-theme-' . $file_name . '.' . $file_ext;
			$webp_thumbnail_xs = $this->convert_to_webp($file->getPathname());

			Storage::disk('public')->put($this->path_thumbnail . $file_name_fix, $webp_thumbnail_xs);
			$data['thumbnail_xs'] = $file_name_fix;
		}
		// end upload thumbnail xs

		if (empty($request->id)) {
			$output = ThemesModel::create($data);
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
			$output = ThemesModel::where('id', $request->id)->update($data);
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
		$find = ThemesModel::find($id, ['background', 'thumbnail', 'thumbnail_xs']);
		$output = ThemesModel::destroy($id);
		if ($output) {
			if ($find->background && Storage::disk('public')->exists($this->path_background . $find->background)) {
				Storage::disk('public')->delete($this->path_background . $find->background);
			}
			if ($find->thumbnail && Storage::disk('public')->exists($this->path_thumbnail . $find->thumbnail)) {
				Storage::disk('public')->delete($this->path_thumbnail . $find->thumbnail);
			}
			if ($find->thumbnail_xs && Storage::disk('public')->exists($this->path_thumbnail . $find->thumbnail_xs)) {
				Storage::disk('public')->delete($this->path_thumbnail . $find->thumbnail_xs);
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
