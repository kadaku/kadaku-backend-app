<?php

namespace App\Http\Controllers;

use App\Models\BrandModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\File;

class BrandController extends Controller
{
    function index()
    {
        return view('brand.index');
    }

    function show(int $id)
    {
        $data = BrandModel::find($id);
        if ($data) {
            if ($data->logo) $data->logo = asset('storage/images/brand/'.base64_decode($data->logo).'.'.$data->logo_ext);
            if ($data->logo_light) $data->logo_light = asset('storage/images/brand/'.base64_decode($data->logo_light).'.'.$data->logo_light_ext);    
            if ($data->favicon) $data->favicon = asset('storage/images/brand/'.base64_decode($data->favicon).'.'.$data->favicon_ext);    
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
            // 'email' => 'required|string|email',
            // 'file_brand' => [
            //     'required', 
            //     File::image()->max(5024)
            // ],
            // 'file_brand_light' => [
            //     'required', 
            //     File::image()->max(5024)
            // ],
            // 'file_brand_favicon' => [
            //     'required', 
            //     File::image()->max(5024)
            // ],
        ]);
        $param_error['validate'] = false;
        if ($validator->fails()) {
            $data_error = $validator->errors();
            $param_error['validate'] = true;
            $param_error['data_error'] = $data_error;
            return response()->json($param_error, 200);
        }
        // end validation

        $data_old = BrandModel::find($request->id);
        
        $file_brand = $request->file('file_brand');
        $file_brand_ext = '';
        $file_brand_path = '';
        if ($file_brand) {
            Storage::delete('public/images/brand/'.base64_decode($data_old->logo).'.'.$data_old->logo_ext);
            $file_brand_ext = $file_brand->getClientOriginalExtension();
            $file_brand_name = 'logo';
            $file_brand_path = $file_brand->storeAs('images/brand', $file_brand_name.'.'.$file_brand->getClientOriginalExtension(), 'public');
        }

        if ($file_brand_path !== '') $file_brand_name = base64_encode($file_brand_name);

        $file_brand_light = $request->file('file_brand_light');
        $file_brand_light_ext = '';
        $file_brand_light_path = '';
        if ($file_brand_light) {
            Storage::delete('public/images/brand/'.base64_decode($data_old->logo_light).'.'.$data_old->logo_light_ext);
            $file_brand_light_ext = $file_brand_light->getClientOriginalExtension();
            $file_brand_light_name = 'logo_light';
            $file_brand_light_path = $file_brand_light->storeAs('images/brand', $file_brand_light_name.'.'.$file_brand_light->getClientOriginalExtension(), 'public');
        }

        if ($file_brand_light_path !== '') $file_brand_light_name = base64_encode($file_brand_light_name);

        $favicon = $request->file('file_brand_favicon');
        $file_favicon_ext = '';
        $file_favicon_path = '';
        if ($favicon) {
            Storage::delete('public/images/brand/'.base64_decode($data_old->favicon).'.'.$data_old->favicon_ext);
            $file_favicon_ext = $favicon->getClientOriginalExtension();
            $file_favicon_name = 'favicon';
            $file_favicon_path = $favicon->storeAs('images/brand', $file_favicon_name.'.'.$favicon->getClientOriginalExtension(), 'public');
        }

        if ($file_favicon_path !== '') $file_favicon_name = base64_encode($file_favicon_name);

        $data = [
            'name' => $request->name,
            'email' => $request->email ?? $request->email,
            'address' => $request->address ?? $request->address,
            'phone_code' => 'ID',
            'phone_dial_code' => '62',
            'phone' => $request->phone ?? $request->phone,
            'logo' => $file_brand_path ? $file_brand_name : $data_old->logo,
            'logo_ext' => $file_brand_path ? $file_brand_ext : $data_old->logo_ext,
            'logo_light' => $file_brand_light_path ? $file_brand_light_name : $data_old->logo_light,
            'logo_light_ext' => $file_brand_light_path ? $file_brand_light_ext : $data_old->logo_light_ext,
            'favicon' => $file_favicon_path ? $file_favicon_name : $data_old->favicon,
            'favicon_ext' => $file_favicon_path ? $file_favicon_ext : $data_old->favicon_ext,
            'youtube' => $request->youtube ?? $request->youtube,
            'instagram' => $request->instagram ?? $request->instagram,
            'facebook' => $request->facebook ?? $request->facebook,
            'twitter' => $request->twitter ?? $request->twitter,
        ];

        if (empty($request->id)) {
            $output = BrandModel::create($data);
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
            $output = BrandModel::where('id', $request->id)->update($data);
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
}
