<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BrandModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BrandController extends Controller
{
    function index()
    {
        $data = BrandModel::find(1);
        if ($data) {
            if ($data->logo) $data->logo = asset('storage/images/brand/'.base64_decode($data->logo).'.'.$data->logo_ext);
            if ($data->logo_light) $data->logo_light = asset('storage/images/brand/'.base64_decode($data->logo_light).'.'.$data->logo_light_ext);    
            if ($data->favicon) $data->favicon = asset('storage/images/brand/'.base64_decode($data->favicon).'.'.$data->favicon_ext);    
            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => $this->message_data_found,
                'data' => $data,
            ], Response::HTTP_OK);
        } else {
            return response()->json([
                'code' => 400,
                'status' => false,
                'message' => $this->message_data_not_found,
            ], Response::HTTP_OK);
        }
    }
}
