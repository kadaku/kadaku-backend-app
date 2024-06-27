<?php

namespace App\Http\Controllers;

use App\Models\IconsPhosporModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IconsPhosporController extends Controller
{
	function index()
	{
		$data['title'] = 'Icons Phospor';
		return view('icons.phospor.index', $data);
	}

	function list_icons(Request $request)
	{
		$url = asset('icons/phosphor/fill/selection.json');
		$contextOptions = [
			"ssl" => [
				"verify_peer" => false,
				"verify_peer_name" => false,
			],
		];
		$context = stream_context_create($contextOptions);
		$json_content = file_get_contents($url, false, $context);
		if ($json_content === false) {
			return response()->json(['error' => 'Failed to fetch the JSON content'], 500);
		}
		$data = json_decode($json_content);
		$properties = [];
		if ($data) {
			$no = 1;
			foreach ($data->icons as $i => $row) {

				$icons = $row->properties->name;
				$icons = str_replace('-fill', '', $icons);

				$name_icon = str_replace('-', ' ', $icons);
				$name_icon = ucwords($name_icon);

				$properties = [
					// 'id' => $no++,
					'name' => $name_icon,
					'icon' => 'ph-' . $icons,
					'type' => 'phosphor',
					'is_active' => 0,
				];

				// IconsPhosporModel::create($properties);
			}
		}
		if ($properties) {
			$content = [
				'code' => 200,
				'status' => true,
				'message' => $this->message_data_found,
				'data' => $properties,
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
			'type' => $request->type,
			'is_all' => isset($_GET['is_all']) ? 1 : 0,
		];

		$limit = 10;
		$start = (((int) $request->page - 1) * $limit);

		if ($param_search['is_all']) {
			$start = 0;
			$limit = 0;
		}

		$model = new IconsPhosporModel();
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
			$output = IconsPhosporModel::where('id', $request->id)->update(['is_active' => 0]);
		} else if ($request->status == 0) {
			$output = IconsPhosporModel::where('id', $request->id)->update(['is_active' => 1]);
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
		$data = IconsPhosporModel::find($id);
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

	function store(Request $request)
	{
		// validation
		$validator = Validator::make($request->all(), [
			'name' => 'required|string|max:100',
			// 'type' => 'required|string|max:100',
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
			'name' => $request->name,
			'type' => $request->type,
		];

		if (empty($request->id)) {
			$output = IconsPhosporModel::create($data);
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
			$output = IconsPhosporModel::where('id', $request->id)->update($data);
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
		$output = IconsPhosporModel::destroy($id);
		if ($output) {
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
