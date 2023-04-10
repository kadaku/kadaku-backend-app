<?php

namespace App\Http\Controllers;

use App\Models\PrivilegesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PrivilegesController extends Controller
{
    function index()
    {
        return view('privileges.index');
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
        
        $model = new PrivilegesModel();
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

    function list_privileges(Request $request)
    {
        $user_group_id = $request->id;
        $admin_model = new PrivilegesModel();
        $data = $admin_model->list_data_privileges($user_group_id);
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

    function show(int $id)
    {
        $data = PrivilegesModel::find($id);
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
        ];

        if (empty($request->id)) {
            $output = PrivilegesModel::create($data);
            if ($output) {
                if (isset($request->menu_id)) {
                    if (is_array($request->menu_id)) {
                        $menus = [];
                        foreach ($request->menu_id as $i => $menu_id) {
                            $sub_data['user_group_id'] = $output->id;
                            $sub_data['menu_id'] = $menu_id;
                            $menus = $sub_data;
                        }
                        DB::table('c_privilege_menus')->insert($menus);
                    }
                }
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
            $output = PrivilegesModel::where('id', $request->id)->update($data);
            if ($output) {
                DB::table('c_privilege_menus')->where('user_group_id', "=", $request->id)->delete();
                if (isset($request->menu_id)) {
                    if (is_array($request->menu_id)) {
                        $menus = [];
                        foreach ($request->menu_id as $i => $menu_id) {
                            $sub_data['user_group_id'] = $request->id;
                            $sub_data['menu_id'] = $menu_id;
                            $menus[] = $sub_data;
                        }
                        DB::table('c_privilege_menus')->insert($menus);
                    }
                }
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
        $output = PrivilegesModel::destroy($id);
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