<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\API\InvitationsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InvitationsController extends Controller
{
    function create(Request $request) {
        $validator = Validator::make($request->all(), [
            'domain' => 'required|string|max:100',
            'category_id' => 'required|int',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'code' => 200,
                'status' => false,
                'data' => $validator->errors(),
            ], 200);
        }

        if (empty($request->theme_id)) {
            $response = [
                'code' => 400,
                'status' => false,
                'message' => 'Please select a theme first',
            ];
            return response()->json($response, 200);
        }

        // cek domain available
        $domain = str_replace(' ', '', trim($request->domain));
        $check_domain = DB::table('t_invitations')->where('domain', $domain)->count();
        if ($check_domain > 0) {
            // if domain is used
            return response()->json([
                'code' => 200,
                'status' => false,
                'data' => [
                    'domain' => ['The domain is already in use, please find another domain'],
                ],
            ], 200);
        } else {
            // if not used
            // data user login
            $data_customer = Auth::user();
            if ($data_customer) {

                // check if trial and not premium account just can create one invitation
                //!! not yet
                
                $customer_id = $data_customer->id;
                $category_id = $request->category_id;
                $theme_id = $request->theme_id;
    
                $data = [
                    'customer_id' => $customer_id,
                    'category_id' => $category_id,
                    'theme_id' => $theme_id,
                    'domain' => $domain,
                ];
    
                $invitation = InvitationsModel::create($data);
                if ($invitation) {
                    $theme_component = DB::table('t_theme_components')->where('theme_id', $theme_id)->get();
                    if ($theme_component) {
                        $data_theme_component = [];
                        foreach ($theme_component as $key => $value) {
                            $data_theme_component[] = [
                                'theme_id' => $theme_id,
                                'invitation_id' => $invitation->id,
                                'customer_id' => $customer_id,
                                'name' => $value->name,
                                'type' => $value->type,
                                'ref' => $value->ref,
                                'order' => $value->order,
                                'props' => $value->props,
                                'icon' => $value->icon,
                                'is_icon' => $value->is_icon,
                                'thumbnail' => $value->thumbnail,
                                'is_premium' => $value->is_premium,
                                'is_active' => $value->is_active,
                            ];
                        }
                        DB::table('t_theme_components')->insert($data_theme_component);
                    }
    
                    return response()->json([
                        'code' => 200,
                        'status' => true,
                        'message' => 'Successfully created an invitation card',
                        'data' => [
                            'invitation_id' => $invitation->id,
                        ]
                    ], 200);
                } else {
                    return response()->json([
                        'code' => 400,
                        'status' => false,
                        'message' => 'Failed to create invitation card',
                    ], 200);
                }
            } else {
                return response()->json([
                    'code' => 400,
                    'status' => false,
                    'message' => 'Forbidden Access',
                ], 200);
            }
        }
    }

    function list()
    {
        $data = InvitationsModel::paginate(10);
        if ($data) {
            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Data Found',
                'data' => $data,
            ], 200);
        } else {
            return response()->json([
                'code' => 400,
                'status' => false,
                'message' => 'Data Not Found',
            ], 200);
        }
    }

    function get($id)
    {
        $data_customer = Auth::user();
        $data = InvitationsModel::where('id', '=', $id)->where('customer_id', $data_customer->id)->first();
        if ($data) {
            $data_theme = DB::table('m_themes')->where('id', $data->theme_id)->first();
            $data->theme = $data_theme;
            
            $data_theme_component = DB::table('t_theme_components')->where('invitation_id', $id)->get();
            $data->sections = $data_theme_component;

            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Data Found',
                'data' => $data,
            ], 200);
        } else {
            return response()->json([
                'code' => 400,
                'status' => false,
                'message' => 'Data Not Found',
            ], 200);
        }
    }

    function get_by_domain($domain = '')
    {
        if (isset($domain) && empty($domain)) {
            return response()->json([
                'code' => 400,
                'status' => false,
                'message' => 'Bad Request',
            ], 200);
        }
        $data = InvitationsModel::where('domain', '=', $domain)->first();
        if ($data) {
            $data_theme = DB::table('m_themes')->where('id', $data->theme_id)->first();
            $data->theme = $data_theme;
            
            $data_theme_component = DB::table('t_theme_components')->where('invitation_id', $data->id)->get();
            $data->sections = $data_theme_component;

            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Data Found',
                'data' => $data,
            ], 200);
        } else {
            return response()->json([
                'code' => 400,
                'status' => false,
                'message' => 'Data Not Found',
            ], 200);
        }
    }
}
