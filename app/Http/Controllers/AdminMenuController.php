<?php

namespace App\Http\Controllers;

use App\Models\AdminMenuModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminMenuController extends Controller
{
    function index()
    {
        return view('admin_menu.index');
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

        $tipedata = 'list';
        if (isset($request->tipedata)) {
            $tipedata = $request->tipedata;
        }
        $param_search = [
            'keyword' => $request->keyword,
        ];

        $limit = 10;
        $start = (((int) $request->page - 1) * $limit);
        
        $admin_model = new AdminMenuModel;
        $data = $admin_model->list_data($start, $limit, $param_search);
        // if ($tipedata === 'list') {
        //     $data = $admin_model->list_data($start, $limit);
        // } else {
        //     $data = $admin_model->list_data_search($start, $limit, $param_search);
        // }

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

    function show(int $id)
    {
        $data = AdminMenuModel::find($id);
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

    function store(Request $request) {
        // validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'path' => 'required|string',
            'icon' => 'required|string|max:200',
            'position' => 'required|int',
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
            'parent_id' => $request->parent_id ?? $request->parent_id,
            'name' => $request->name,
            'url' => $request->path,
            'icon' => $request->icon,
            'sort' => $request->position,
        ];

        if (empty($request->id)) {
            $output = AdminMenuModel::create($data);
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
            $output = AdminMenuModel::where('id', $request->id)->update($data);
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
        $output = AdminMenuModel::destroy($id);
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
