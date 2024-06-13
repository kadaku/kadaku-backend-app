<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\API\MusicsModel as MusicsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MusicsController extends Controller
{
	function list_categories_musics(Request $request) 
	{
		$data = DB::table("m_categories_musics")->select("id", "name")->where("is_active", "=", 1)->get();
		if (count($data) > 0) {
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
				'data' => [],
			];
			return response()->json($content, 200);
		}
	}

	function list_musics(Request $request)
	{
		if (empty($request->page)) {
			return response()->json([
				'code' => 400,
				'status' => false,
				'message' => 'Bad Request',
			], 400);
		}

		$param_search = [
			'keyword' => $request->q,
			'category' => $request->category,
		];

		$limit = 10;
		$start = (((int) $request->page - 1) * $limit);

		$model = new MusicsModel();
		$data = $model->list_data($start, $limit, $param_search);

		$data['page'] = (int) $request->page;
		$data['limit'] = $limit;

		if (count($data['list']) > 0) {
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
				'data' => [
					'list' => [],
					'total' => 0,
				],
			];
			return response()->json($content, 200);
		}
	}
}
