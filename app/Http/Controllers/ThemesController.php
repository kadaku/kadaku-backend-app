<?php

namespace App\Http\Controllers;

use App\Models\AssetMediaModel;
use App\Models\ThemesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ThemesController extends Controller
{
	protected $path;
	protected $path_thumbnail;
	protected $path_background;

	function __construct()
	{
		$this->path = 'images/themes';
	}

	function index()
	{
		$data['title'] = 'Themes';
		$data['categories_themes'] = DB::table('m_categories')->where('is_active', 1)->get();
		$data['themes_type'] = DB::table('m_themes_type')->where('is_active', 1)->get();
		$data['categories_asset_media'] = AssetMediaModel::list_categories();
		return view('themes.index', $data);
	}

	function list(Request $request)
	{
		if (empty($request->page)) {
			return response()->json([
				'code' => 400,
				'status' => false,
				'message' => $this->message_bad_request,
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
			foreach ($data['list'] as $i => $value) {
				$data['list'][$i]->url_thumbnail = NULL;
				if ($value->thumbnail && Storage::disk('public')->exists($this->path . $value->thumbnail)) {
					$data['list'][$i]->url_thumbnail = asset('storage/' . $this->path . $value->thumbnail);
				}
				$data['list'][$i]->url_thumbnail_xs = NULL;
				if ($value->thumbnail_xs && Storage::disk('public')->exists($this->path . $value->thumbnail_xs)) {
					$data['list'][$i]->url_thumbnail_xs = asset('storage/' . $this->path . $value->thumbnail_xs);
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
				'message' => $this->message_bad_request,
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

	function show(int $id)
	{
		if (empty($id)) {
			return response()->json([
				'code' => 400,
				'status' => false,
				'message' => $this->message_bad_request,
			], 200);
		}

		$data = ThemesModel::find($id);
		if ($data) {
			$data->music = NULL;
			if ($data->music_id) {
				$data->music = DB::table('m_musics')->where('id', $data->id)->get();
			}

			$data->url_thumbnail = NULL;
			if ($data->thumbnail && Storage::disk('public')->exists($this->path . $data->thumbnail)) {
				$data->url_thumbnail = asset('storage/' . $this->path . $data->thumbnail);
			}

			$data->url_thumbnail_xs = NULL;
			if ($data->thumbnail_xs && Storage::disk('public')->exists($this->path . $data->thumbnail_xs)) {
				$data->url_thumbnail_xs = asset('storage/' . $this->path . $data->thumbnail_xs);
			}

			$data->url_frame_top_left = NULL;
			if ($data->frame_top_left && Storage::disk('public')->exists($this->path . $data->frame_top_left)) {
				$data->url_frame_top_left = asset('storage/' . $this->path . $data->frame_top_left);
			}

			$data->url_frame_top_center = NULL;
			if ($data->frame_top_center && Storage::disk('public')->exists($this->path . $data->frame_top_center)) {
				$data->url_frame_top_center = asset('storage/' . $this->path . $data->frame_top_center);
			}

			$data->url_frame_top_right = NULL;
			if ($data->frame_top_right && Storage::disk('public')->exists($this->path . $data->frame_top_right)) {
				$data->url_frame_top_right = asset('storage/' . $this->path . $data->frame_top_right);
			}

			$data->url_frame_side_left = NULL;
			if ($data->frame_side_left && Storage::disk('public')->exists($this->path . $data->frame_side_left)) {
				$data->url_frame_side_left = asset('storage/' . $this->path . $data->frame_side_left);
			}

			$data->url_background = NULL;
			if ($data->background && Storage::disk('public')->exists($this->path . $data->background)) {
				$data->url_background = asset('storage/' . $this->path . $data->background);
			}

			$data->url_frame_side_right = NULL;
			if ($data->frame_side_right && Storage::disk('public')->exists($this->path . $data->frame_side_right)) {
				$data->url_frame_side_right = asset('storage/' . $this->path . $data->frame_side_right);
			}

			$data->url_frame_bottom_left = NULL;
			if ($data->frame_bottom_left && Storage::disk('public')->exists($this->path . $data->frame_bottom_left)) {
				$data->url_frame_bottom_left = asset('storage/' . $this->path . $data->frame_bottom_left);
			}

			$data->url_frame_bottom_center = NULL;
			if ($data->frame_bottom_center && Storage::disk('public')->exists($this->path . $data->frame_bottom_center)) {
				$data->url_frame_bottom_center = asset('storage/' . $this->path . $data->frame_bottom_center);
			}

			$data->url_frame_bottom_right = NULL;
			if ($data->frame_bottom_right && Storage::disk('public')->exists($this->path . $data->frame_bottom_right)) {
				$data->url_frame_bottom_right = asset('storage/' . $this->path . $data->frame_bottom_right);
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
			'music_id' => 'required|numeric',
			'name' => 'required|string|max:200',
			'layout' => 'required|string',
			'thumbnail' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
			'thumbnail_xs' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
			
			'font' => 'required|string|max:200',
			'font_base' => 'required|string|max:200',
			'font_accent' => 'required|string|max:200',
			'font_latin' => 'required|string|max:200',
			'inv_bg' => 'required|string|max:200',
			'inv_base' => 'required|string|max:200',
			'inv_accent' => 'required|string|max:200',
			'inv_border' => 'required|string|max:200',
			'menu_bg' => 'required|string|max:200',
			'menu_inactive' => 'required|string|max:200',
			'menu_active' => 'required|string|max:200',
			'btn_color' => 'required|string|max:200',

			'frame_top_left' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
			'frame_top_center' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
			'frame_top_right' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
			'frame_side_left' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
			'background' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
			'frame_side_right' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
			'frame_bottom_left' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
			'frame_bottom_center' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
			'frame_bottom_right' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
		]);

		$param_error['validate'] = false;
		if ($validator->fails()) {
			$data_error = $validator->errors();
			$param_error['validate'] = true;
			$param_error['data_error'] = $data_error;
			return response()->json($param_error, 200);
		}
		// end validations

		$id = NULL;
		if (!empty($request->id)) {
			$id = $request->id;
		}

		$styles_root = [
			'font_base' => $request->font_base,
			'font_accent' => $request->font_accent,
			'font_latin' => $request->font_latin,
			'inv_bg' => $request->inv_bg,
			'inv_base' => $request->inv_base,
			'inv_accent' => $request->inv_accent,
			'inv_border' => $request->inv_border,
			'menu_bg' => $request->menu_bg,
			'menu_inactive' => $request->menu_inactive,
			'menu_active' => $request->menu_active,
			'btn_color' => $request->btn_color,
		];

		$slug = Str::of($request->name)->slug('-');
		$data = [
			'category_id' => $request->category_id,
			'type_id' => $request->type_id,
			'name' => $request->name,
			'slug' => $slug,
			'description' => $request->description ?? $request->description,
			'fonts_import' => $request->font,
			'styles_root' => json_encode($styles_root),
			'layout' => $request->layout,
			'music_id' => $request->music_id,
			'price' => !empty($request->price) ? currency_to_number($request->price) : 0,
			'discount' => !empty($request->discount) ? $request->discount : 0,
			'is_premium' => isset($_POST['is_premium']) ? $request->is_premium : 0,
			'version' => isset($_POST['version']) ? $request->version : 1,
			'is_active' => 0,
		];

		$directory_path = public_path($this->path . '/' . $slug);
		if (!Storage::disk('public')->exists($directory_path)) {
			Storage::disk('public')->makeDirectory($directory_path, 777, true);
		}

		// upload background
		if ($request->file('background')) {
			// remove the old avatar if it exists
			if ($request->background_old && Storage::disk('public')->exists($this->path . $request->background_old)) {
				Storage::disk('public')->delete($this->path . $request->background_old);
			}
			
			$file = $request->file('background');
			$file_ext = 'webp';
			$file_name = '/' . $slug . '/' . 'bg' . '.' . $file_ext;
			$webp_background = $this->convert_to_webp($file->getPathname());

			Storage::disk('public')->put($this->path . $file_name, $webp_background);
			$data['background'] = $file_name;
		}
		// end upload background

		// TODO: for frame
		// upload frame top left
		if ($request->file('frame_top_left')) {
			// remove the old frame top left if it exists
			if ($request->frame_top_left_old && Storage::disk('public')->exists($this->path . $request->frame_top_left_old)) {
				Storage::disk('public')->delete($this->path . $request->frame_top_left_old);
			}
			
			$file = $request->file('frame_top_left');
			$file_ext = 'webp';
			$file_name = '/' . $slug . '/' . 'tl' . '.' . $file_ext;
			$webp_frame_top_left = $this->convert_to_webp($file->getPathname());

			Storage::disk('public')->put($this->path . $file_name, $webp_frame_top_left);
			$data['frame_top_left'] = $file_name;
		}
		// end upload frame top left
		
		// upload frame top center
		if ($request->file('frame_top_center')) {
			// remove the old frame top center if it exists
			if ($request->frame_top_center_old && Storage::disk('public')->exists($this->path . $request->frame_top_center_old)) {
				Storage::disk('public')->delete($this->path . $request->frame_top_center_old);
			}
			
			$file = $request->file('frame_top_center');
			$file_ext = 'webp';
			$file_name = '/' . $slug . '/' . 'tc' . '.' . $file_ext;
			$webp_frame_top_center = $this->convert_to_webp($file->getPathname());

			Storage::disk('public')->put($this->path . $file_name, $webp_frame_top_center);
			$data['frame_top_center'] = $file_name;
		}
		// end upload frame top center

		// upload frame top right
		if ($request->file('frame_top_right')) {
			// remove the old frame top right if it exists
			if ($request->frame_top_right_old && Storage::disk('public')->exists($this->path . $request->frame_top_right_old)) {
				Storage::disk('public')->delete($this->path . $request->frame_top_right_old);
			}
			
			$file = $request->file('frame_top_right');
			$file_ext = 'webp';
			$file_name = '/' . $slug . '/' . 'tr' . '.' . $file_ext;
			$webp_frame_top_right = $this->convert_to_webp($file->getPathname());

			Storage::disk('public')->put($this->path . $file_name, $webp_frame_top_right);
			$data['frame_top_right'] = $file_name;
		}

		// upload frame side left
		if ($request->file('frame_side_left')) {
			// remove the old frame side left if it exists
			if ($request->frame_side_left_old && Storage::disk('public')->exists($this->path . $request->frame_side_left_old)) {
				Storage::disk('public')->delete($this->path . $request->frame_side_left_old);
			}
			
			$file = $request->file('frame_side_left');
			$file_ext = 'webp';
			$file_name = '/' . $slug . '/' . 'left' . '.' . $file_ext;
			$webp_frame_side_left = $this->convert_to_webp($file->getPathname());

			Storage::disk('public')->put($this->path . $file_name, $webp_frame_side_left);
			$data['frame_side_left'] = $file_name;
		}
		// end upload frame side left

		// upload frame side right
		if ($request->file('frame_side_right')) {
			// remove the old frame side right if it exists
			if ($request->frame_side_right_old && Storage::disk('public')->exists($this->path . $request->frame_side_right_old)) {
				Storage::disk('public')->delete($this->path . $request->frame_side_right_old);
			}
			
			$file = $request->file('frame_side_right');
			$file_ext = 'webp';
			$file_name = '/' . $slug . '/' . 'right' . '.' . $file_ext;
			$webp_frame_side_right = $this->convert_to_webp($file->getPathname());

			Storage::disk('public')->put($this->path . $file_name, $webp_frame_side_right);
			$data['frame_side_right'] = $file_name;
		}
		// end upload frame side right

		// upload frame bottom left
		if ($request->file('frame_bottom_left')) {
			// remove the old frame bottom left if it exists
			if ($request->frame_bottom_left_old && Storage::disk('public')->exists($this->path . $request->frame_bottom_left_old)) {
				Storage::disk('public')->delete($this->path . $request->frame_bottom_left_old);
			}
			
			$file = $request->file('frame_bottom_left');
			$file_ext = 'webp';
			$file_name = '/' . $slug . '/' . 'bl' . '.' . $file_ext;
			$webp_frame_bottom_left = $this->convert_to_webp($file->getPathname());

			Storage::disk('public')->put($this->path . $file_name, $webp_frame_bottom_left);
			$data['frame_bottom_left'] = $file_name;
		}
		// end upload frame bottom left
		
		// upload frame bottom center
		if ($request->file('frame_bottom_center')) {
			// remove the old frame bottom center if it exists
			if ($request->frame_bottom_center_old && Storage::disk('public')->exists($this->path . $request->frame_bottom_center_old)) {
				Storage::disk('public')->delete($this->path . $request->frame_bottom_center_old);
			}
			
			$file = $request->file('frame_bottom_center');
			$file_ext = 'webp';
			$file_name = '/' . $slug . '/' . 'bc' . '.' . $file_ext;
			$webp_frame_bottom_center = $this->convert_to_webp($file->getPathname());

			Storage::disk('public')->put($this->path . $file_name, $webp_frame_bottom_center);
			$data['frame_bottom_center'] = $file_name;
		}
		// end upload frame bottom center

		// upload frame bottom right
		if ($request->file('frame_bottom_right')) {
			// remove the old frame bottom right if it exists
			if ($request->frame_bottom_right_old && Storage::disk('public')->exists($this->path . $request->frame_bottom_right_old)) {
				Storage::disk('public')->delete($this->path . $request->frame_bottom_right_old);
			}
			
			$file = $request->file('frame_bottom_right');
			$file_ext = 'webp';
			$file_name = '/' . $slug . '/' . 'br' . '.' . $file_ext;
			$webp_frame_bottom_right = $this->convert_to_webp($file->getPathname());

			Storage::disk('public')->put($this->path . $file_name, $webp_frame_bottom_right);
			$data['frame_bottom_right'] = $file_name;
		}
		// end upload frame bottom right
		// TODO: for frame

		// upload thumbnail
		if ($request->file('thumbnail')) {
			// remove the old thumbnail if it exists
			if ($request->thumbnail_old && Storage::disk('public')->exists($this->path . $request->thumbnail_old)) {
				Storage::disk('public')->delete($this->path . $request->thumbnail_old);
			}
			
			$file = $request->file('thumbnail');
			$file_ext = 'webp';
			$file_name = '/' . $slug . '/' . 'thumbnail' . '.' . $file_ext;
			$webp_thumbnail = $this->convert_to_webp($file->getPathname());

			Storage::disk('public')->put($this->path . $file_name, $webp_thumbnail);
			$data['thumbnail'] = $file_name;
		}
		// end upload thumbnail

		// upload thumbnail xs
		if ($request->file('thumbnail_xs')) {
			// remove the old thumbnail xs if it exists
			if ($request->thumbnail_xs_old && Storage::disk('public')->exists($this->path . $request->thumbnail_xs_old)) {
				Storage::disk('public')->delete($this->path . $request->thumbnail_xs_old);
			}
			
			$file = $request->file('thumbnail_xs');
			$file_ext = 'webp';
			$file_name = '/' . $slug . '/' . 'thumbnail-xs' . '.' . $file_ext;
			$webp_thumbnail_xs = $this->convert_to_webp($file->getPathname());

			Storage::disk('public')->put($this->path . $file_name, $webp_thumbnail_xs);
			$data['thumbnail_xs'] = $file_name;
		}
		// end upload thumbnail xs

		if (empty($id)) {
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
		if (empty($id)) {
			return response()->json([
				'code' => 400,
				'status' => false,
				'message' => $this->message_bad_request,
			], 200);
		}

		$find = ThemesModel::find($id);
		$output = ThemesModel::destroy($id);
		if ($output) {
			if ($find->thumbnail && Storage::disk('public')->exists($this->path . $find->thumbnail)) {
				Storage::disk('public')->delete($this->path . $find->thumbnail);
			}
			if ($find->thumbnail_xs && Storage::disk('public')->exists($this->path . $find->thumbnail_xs)) {
				Storage::disk('public')->delete($this->path . $find->thumbnail_xs);
			}

			if ($find->frame_top_left && Storage::disk('public')->exists($this->path . $find->frame_top_left)) {
				Storage::disk('public')->delete($this->path . $find->frame_top_left);
			}

			if ($find->frame_top_center && Storage::disk('public')->exists($this->path . $find->frame_top_center)) {
				Storage::disk('public')->delete($this->path . $find->frame_top_center);
			}

			if ($find->frame_top_right && Storage::disk('public')->exists($this->path . $find->frame_top_right)) {
				Storage::disk('public')->delete($this->path . $find->frame_top_right);
			}

			if ($find->frame_side_left && Storage::disk('public')->exists($this->path . $find->frame_side_left)) {
				Storage::disk('public')->delete($this->path . $find->frame_side_left);
			}

			if ($find->background && Storage::disk('public')->exists($this->path . $find->background)) {
				Storage::disk('public')->delete($this->path . $find->background);
			}

			if ($find->frame_side_right && Storage::disk('public')->exists($this->path . $find->frame_side_right)) {
				Storage::disk('public')->delete($this->path . $find->frame_side_right);
			}

			if ($find->frame_bottom_left && Storage::disk('public')->exists($this->path . $find->frame_bottom_left)) {
				Storage::disk('public')->delete($this->path . $find->frame_bottom_left);
			}

			if ($find->frame_bottom_center && Storage::disk('public')->exists($this->path . $find->frame_bottom_center)) {
				Storage::disk('public')->delete($this->path . $find->frame_bottom_center);
			}

			if ($find->frame_bottom_right && Storage::disk('public')->exists($this->path . $find->frame_bottom_right)) {
				Storage::disk('public')->delete($this->path . $find->frame_bottom_right);
			}

			if (Storage::disk('public')->exists($this->path . '/' . $find->slug)) {
				Storage::disk('public')->deleteDirectory($this->path . '/' . $find->slug);
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

	function destroy_image(Request $request) 
	{
		$id = $request->id;
		$field = $request->field;

		if (empty($id) && empty($field)) {
			$content = [
				'code' => 400,
				'status' => false,
				'message' => $this->message_bad_request,
			];
			return response()->json($content, 200);
		}

		$image = ThemesModel::select($field)->find($id)->$field;
		if (Storage::disk('public')->exists($this->path . $image)) {
			$response = Storage::disk('public')->delete($this->path . $image);
			if ($response) {
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
		} else {
			$content = [
				'code' => 400,
				'status' => false,
				'message' => $this->message_destroy_failed,
			];
			return response()->json($content, 200);
		}
	}

	function show_components(int $id) {
		if (empty($id)) {
			return response()->json([
				'code' => 400,
				'status' => false,
				'message' => $this->message_bad_request,
			], 200);
		}

		$data = ThemesModel::find($id);
		$data->component = [];
		if ($data) {
			$data->url_frame_top_left = NULL;
			if ($data->frame_top_left && Storage::disk('public')->exists($this->path . $data->frame_top_left)) {
				$data->url_frame_top_left = asset('storage/' . $this->path . $data->frame_top_left);
			}

			$data->url_frame_top_center = NULL;
			if ($data->frame_top_center && Storage::disk('public')->exists($this->path . $data->frame_top_center)) {
				$data->url_frame_top_center = asset('storage/' . $this->path . $data->frame_top_center);
			}

			$data->url_frame_top_right = NULL;
			if ($data->frame_top_right && Storage::disk('public')->exists($this->path . $data->frame_top_right)) {
				$data->url_frame_top_right = asset('storage/' . $this->path . $data->frame_top_right);
			}

			$data->url_frame_side_left = NULL;
			if ($data->frame_side_left && Storage::disk('public')->exists($this->path . $data->frame_side_left)) {
				$data->url_frame_side_left = asset('storage/' . $this->path . $data->frame_side_left);
			}

			$data->url_background = NULL;
			if ($data->background && Storage::disk('public')->exists($this->path . $data->background)) {
				$data->url_background = asset('storage/' . $this->path . $data->background);
			}

			$data->url_frame_side_right = NULL;
			if ($data->frame_side_right && Storage::disk('public')->exists($this->path . $data->frame_side_right)) {
				$data->url_frame_side_right = asset('storage/' . $this->path . $data->frame_side_right);
			}

			$data->url_frame_bottom_left = NULL;
			if ($data->frame_bottom_left && Storage::disk('public')->exists($this->path . $data->frame_bottom_left)) {
				$data->url_frame_bottom_left = asset('storage/' . $this->path . $data->frame_bottom_left);
			}

			$data->url_frame_bottom_center = NULL;
			if ($data->frame_bottom_center && Storage::disk('public')->exists($this->path . $data->frame_bottom_center)) {
				$data->url_frame_bottom_center = asset('storage/' . $this->path . $data->frame_bottom_center);
			}

			$data->url_frame_bottom_right = NULL;
			if ($data->frame_bottom_right && Storage::disk('public')->exists($this->path . $data->frame_bottom_right)) {
				$data->url_frame_bottom_right = asset('storage/' . $this->path . $data->frame_bottom_right);
			}

			
			$components = DB::table('m_theme_components as tc')
				->select([
					'tc.*',
					DB::raw('(ROW_NUMBER() OVER ( PARTITION BY tc.name ORDER BY tc.order )) as row_num')
				])
				->where('tc.theme_id', $id)
				->where('tc.invitation_id', 0)
				->where('tc.customer_id', 0)
				->orderBy('tc.order', 'asc')
				->orderBy('tc.id', 'asc')
				->get();

			if ($components) {
				foreach ($components as $j => $component) {
					$components[$j]->url_background = NULL;
					if ($component->background && Storage::disk('public')->exists($this->path . $component->background)) {
						$components[$j]->url_background = asset('storage/' . $this->path . $component->background);
					}
				}
			}
			
			$data->components = $components;
		}

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
			];
			return response()->json($content, 200);
		}
	}

	function store_components(Request $request) 
	{
		$theme_id = $request->theme_id;
		$layout = $request->layout;

		if (empty($theme_id) && empty($layout)) {
			return response()->json([
				'code' => 400,
				'status' => false,
				'message' => $this->message_bad_request,
			], 200);
		}

		$layout = json_decode($layout);

		$params = [
			'theme_id' => $theme_id,
			'invitation_id' => 0,
			'customer_id' => 0,
			'name' => $layout->category,
			'type' => 'section',
			'icon' => $layout->icon,
			'is_icon' => 1,
			'body' => $layout->body,
			'order' => $layout->order,
		];
		
		$response = DB::table('m_theme_components')->insert($params);
		if ($response) {
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
	}

	function update_components(Request $request) 
	{
		$id = $request->id;
		$theme_id = $request->theme_id;
		$field = $request->field;
		$value = $request->value;

		if (empty($id) && empty($theme_id) && empty($field)) {
			return response()->json([
				'code' => 400,
				'status' => false,
				'message' => $this->message_bad_request,
			], 200);
		}

		if (empty($value)) $value = NULL;
		
		$param = [];
		if(isset($_POST['is_upload']) && $request->is_upload == 1) { 
			if ($request->file('value')) {
				$data_theme = DB::table('m_themes')->where('id', $theme_id)->first();
				$data_section = DB::table('m_theme_components')->where('id', $id)->where('theme_id', $theme_id)->first();

				if ($data_theme && $data_section) {
					if ($data_section->background && Storage::disk('public')->exists($this->path . $data_section->background)) {
						Storage::disk('public')->delete($this->path . $data_section->background);
					}

					$directory_path = public_path($this->path . '/' . $data_theme->slug . '/sections');
					if (!Storage::disk('public')->exists($directory_path)) {
						Storage::disk('public')->makeDirectory($directory_path, 777, true);
					}

					$file = $request->file('value');
					
					$file_ext = 'webp';
					$file_name = '/' . $data_theme->slug . '/sections/' . 'bg-' . $id .'-' . time() . '.' . $file_ext;
					$webp_background = $this->convert_to_webp($file->getPathname());
		
					Storage::disk('public')->put($this->path . $file_name, $webp_background);
					$param = ['background' => $file_name];
				}
			} else {
				$content = [
					'code' => 400,
					'status' => false,
					'message' => 'No upload file',
				];
				return response()->json($content, 200);
			}
		} else {
			$param = [$field => $value];
		}

		if (!$param) {
			$content = [
				'code' => 400,
				'status' => false,
				'message' => $this->message_bad_request,
			];
			return response()->json($content, 200);
		}

		$response = DB::table('m_theme_components')->where('id', '=', $id)->where('theme_id', '=', $theme_id)->update($param);
		if ($response) {
			$content = [
				'code' => 200,
				'status' => true,
				'message' => $this->message_update_success,
				'data' => [
					'id' => $id,
					'theme_id' => $theme_id,
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

	function destroy_components(Request $request) 
	{
		$id = $request->id;
		$theme_id = $request->theme_id;

		if (empty($id) && empty($theme_id)) {
			return response()->json([
				'code' => 400,
				'status' => false,
				'message' => $this->message_bad_request,
			], 200);
		}

		$find = DB::table('m_theme_components')->where('id', '=', $id)->first();
		$output = DB::table('m_theme_components')->delete($id);
		if ($output) {
			if ($find->background && Storage::disk('public')->exists($this->path . $find->background)) {
				Storage::disk('public')->delete($this->path . $find->background);
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

	function destroy_background_components(int $id) {
		if (empty($id)) {
			$content = [
				'code' => 400,
				'status' => false,
				'message' => $this->message_bad_request,
			];
			return response()->json($content, 200);
		}

		$data = DB::table('m_theme_components')->select('background')->where('id', '=', $id)->first();
		if ($data) {
			$background = $data->background;
			if (Storage::disk('public')->exists($this->path . $background)) {
				$response = Storage::disk('public')->delete($this->path . $background);
				if ($response) {
					DB::table('m_theme_components')->where('id', $id)->update(['background' => NULL]);

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
			} else {
				$content = [
					'code' => 400,
					'status' => false,
					'message' => $this->message_destroy_failed,
				];
				return response()->json($content, 200);
			}
		} else {
			$content = [
				'code' => 400,
				'status' => false,
				'message' => $this->message_destroy_failed . ' 002',
			];
			return response()->json($content, 200);
		}
	}
}
