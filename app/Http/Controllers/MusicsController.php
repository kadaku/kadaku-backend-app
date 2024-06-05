<?php

namespace App\Http\Controllers;

use App\Models\MusicsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MusicsController extends Controller
{
	protected $path;

	function __construct()
	{
		$this->path = 'songs/';
	}

	function index()
	{
		$data['title'] = 'Musics';
		$data['categories_musics'] = DB::table('m_categories_musics')->where('is_active', 1)->get();
		return view('musics.index', $data);
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

		$model = new MusicsModel();
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
			$output = MusicsModel::where('id', $request->id)->update(['is_active' => 0]);
		} else if ($request->status == 0) {
			$output = MusicsModel::where('id', $request->id)->update(['is_active' => 1]);
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
		$data = MusicsModel::find($id);
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
			'category_music_id' => 'required',
			'name' => 'required|string|max:255',
			'file_music' => 'nullable|mimes:mp3',
		]);

		$param_error['validate'] = false;
		if ($validator->fails()) {
			$data_error = $validator->errors();
			$param_error['validate'] = true;
			$param_error['data_error'] = $data_error;
			return response()->json($param_error, 200);
		}
		// end validations

		$data = [
			'category_music_id' => $request->category_music_id,
			'name' => $request->name,
			'categories' => $request->categories,
		];

		// upload		
		if ($request->file('file_music')) {
			// remove the old avatar if it exists
			if ($request->file_music_old && Storage::disk('public')->exists($this->path . $request->file_music_old)) {
				Storage::disk('public')->delete($this->path . $request->file_music_old);
			}
			
			$file = $request->file('file_music');
			$file_ext = $file->getClientOriginalExtension();
			$file_name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
			$file_name = Str::of($file_name)->slug('-');
			$file_name = $file_name . '.' . $file_ext;

			Storage::disk('public')->put($this->path . $file_name, file_get_contents($request->file_music));
			$data['file'] = $file_name;
		}
		// end upload
		if (empty($request->id)) {
			$data['created_by'] = auth()->user()->id;
			$output = MusicsModel::create($data);
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
			$data['modified_by'] = auth()->user()->id;
			$output = MusicsModel::where('id', $request->id)->update($data);
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
		$find = MusicsModel::find($id, ['file']);
		$output = MusicsModel::destroy($id);
		if ($output) {
			if ($find->file && Storage::disk('public')->exists($this->path . $find->file)) {
				Storage::disk('public')->delete($this->path . $find->file);
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

	function scraping_musics()
	{
		$output = NULL;
		$total = 0;
		
		for ($i = 1; $i <= 1; $i++) {
			$page = $i;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://satumomen.com/api/musics?cat=&q=&page=$page");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Linux; Android 4.4.2; Nexus 4 Build/KOT49H) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.114 Mobile Safari/537.36");
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

			$header = array(
				"Accept: application/json",
				"Authorization: Bearer 77548|z6pH05vR0uFmXO4dSpERWsVrndQJ6RM2a1jX2Pr8",
				"Content-Type: application/json",
				"cache-control: no-cache"
			);

			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			$output = curl_exec($ch);
			if ($output) {
				$decode = json_decode($output);
				// echo '<pre>';
				// print_r($decode->data->data);
				// echo '</pre>';
				$total = $decode->data->total;
				if (isset($decode->data->data) && $decode->data->data) {
					foreach ($decode->data->data as $value) {
						$find = MusicsModel::where('temp_id', $value->id)->first();

						$file = preg_replace('/\r\n|\r|\n/', '', $value->file);
						$file_url = preg_replace('/\r\n|\r|\n/', '', $value->file_url);

						$data = [
							'temp_id' => $value->id,
							'name' => $value->name,
							'file' => $file,
							'file_url' => $file_url,
							'categories' => ucwords($value->categories),
						];
						if ($find && ($find->temp_id == $value->id)) {
							// $output = MusicsModel::where('temp_id', $value->id)->update($data);
						} else {
							$output = MusicsModel::create($data);
						}
					}
				}
			}
		}

		if ($output) {
			$content = [
				'code' => 200,
				'status' => true,
				'total' => $total,
				'message' => 'Success scrapping (Total Data : '.$total.')',
			];
			return response()->json($content, 200);
		} else {
			$content = [
				'code' => 400,
				'status' => false,
				'message' => 'Failed scrapping',
			];
			return response()->json($content, 200);
		}
	}

	function scraping_file_musics()
	{
		// for ($i = 1; $i <= 252; $i++) { 
			$page = isset($_GET['page']) ? $_GET['page'] : 1; // current 12
			// $page = $i; // current 12
			$limit = 100;
			$start = (((int) $page - 1) * $limit);
			// $data = DB::table('m_musics')->offset($start)->limit($limit)->get();
			$data = DB::table('m_musics')->get();
			
			// echo '<pre>';
			// print_r($data);
			// echo '</pre>';	
			if ($data) {
				$path = 'songs/';
				foreach ($data as $i => $value) {
					// if ($value->file && !Storage::disk('public')->exists($path . $value->file)) {
					if ($value->file && !file_exists('storage/'.$path . $value->file)) {
						// Define the URL of the file to download
						$url = preg_replace('/\r\n|\r|\n/', '', $value->file_url);
						$file = preg_replace('/\r\n|\r|\n/', '', $value->file);
						$fileName = $path . $file;
						Storage::disk('public')->put($fileName, file_get_contents($url));
						$response_arr = [
							'code' => 200,
							'status' => true, 
							'message' => 'File downloaded successfully'
						];
					} else {
						$response_arr = [
							'code' => 404,
							'status' => false, 
							'message' => 'Nothing file download'
						];
					}
				}
			} else {
				$response_arr = [
					'code' => 404,
					'status' => false, 
					'message' => 'Data musics not found',
				];
			}
			return response()->json($response_arr);
		// }
	}
}
