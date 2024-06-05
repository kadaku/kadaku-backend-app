<?php

namespace App\Http\Controllers;

use App\Models\CategoriesMusicsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoriesMusicsController extends Controller
{
	function index()
	{
		$data['title'] = 'Categories Musics';
		return view('categories_musics.index', $data);
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

		$model = new CategoriesMusicsModel();
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
			$output = CategoriesMusicsModel::where('id', $request->id)->update(['is_active' => 0]);
		} else if ($request->status == 0) {
			$output = CategoriesMusicsModel::where('id', $request->id)->update(['is_active' => 1]);
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
		$data = CategoriesMusicsModel::find($id);
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
			'slug' => Str::of($request->name)->slug('-'),
		];

		if (empty($request->id)) {
			$output = CategoriesMusicsModel::create($data);
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
			$output = CategoriesMusicsModel::where('id', $request->id)->update($data);
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
		$output = CategoriesMusicsModel::destroy($id);
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
