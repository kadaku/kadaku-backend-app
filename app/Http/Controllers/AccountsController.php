<?php

namespace App\Http\Controllers;

use App\Models\AccountsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountsController extends Controller
{
    function index()
    {
        $user_groups = DB::table('c_user_groups')->get();
        return view('accounts.index', compact('user_groups'));
    }

    function list(Request $request)
    {
        if (empty($request->page)) {
            return response()->json([
                'code' => 400,
                'status' => false,
                'message' => 'Parameter page is empty',
            ], 200);
        }

        $param_search = [
            'keyword' => $request->keyword,
        ];

        $limit = 10;
        $start = (((int) $request->page - 1) * $limit);
        
        $model = new AccountsModel();
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
                'message' => 'Parameter is empty',
            ], 200);
        }

        $output = null;
        if ($request->status == 1) {
            $output = AccountsModel::where('id', $request->id)->update(['is_active' => 0]);
        } else if ($request->status == 0) {
            $output = AccountsModel::where('id', $request->id)->update(['is_active' => 1]);
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
        $data = AccountsModel::find($id);
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
        
    }
}
