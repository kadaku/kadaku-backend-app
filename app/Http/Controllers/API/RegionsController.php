<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class RegionsController extends Controller
{
    function propinsi($no_prop = '')
    {
        $data = DB::table('m_propinsi');
        if (!empty($no_prop)) $data->where('no', $no_prop);
        $data = $data->get();

        if (count($data) > 0) {
            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => [
                    'list' => $data,
                    'total' => count($data),
                ],
            ], 200);
        } else {
             return response()->json([
                'code' => 404,
                'status' => false,
                'data' => [
                    'list' => [],
                    'total' => 0,
                ]
            ], 404);
        }
    }

    function kabupaten($no_prop = '', $no_kab = '')
    {
        $data = DB::table('m_kabupaten');
        if (!empty($no_prop)) $data->where('no_propinsi', $no_prop);
        if (!empty($no_kab)) $data->where('no', $no_kab);
        
        $data = $data->get();

        if (count($data) > 0) {
            return response()->json([
                'code' => 200,
                'status' => true,
                'data' => [
                    'list' => $data,
                    'total' => count($data),
                ],
            ], 200);
        } else {
             return response()->json([
                'code' => 404,
                'status' => false,
                'data' => [
                    'list' => [],
                    'total' => 0,
                ]
            ], 404);
        }
    }

    function kecamatan($no_prop = '', $no_kab = '', $no_kec = '')
    {
        $data = DB::table('m_kecamatan');
        if (empty($no_prop) && empty($no_kab)) {
            return response()->json([
                'code' => 400,
                'status' => false,
                'data' => NULL,
            ], 400);
        } else {
            if (!empty($no_prop)) $data->where('no_propinsi', $no_prop);
            if (!empty($no_kab)) $data->where('no_kabupaten', $no_kab);
            if (!empty($no_kec)) $data->where('no', $no_kec);
            
            $data = $data->get();
    
            if (count($data) > 0) {
                return response()->json([
                    'code' => 200,
                    'status' => true,
                    'data' => [
                        'list' => $data,
                        'total' => count($data),
                    ],
                ], 200);
            } else {
                 return response()->json([
                    'code' => 404,
                    'status' => false,
                    'data' => [
                        'list' => [],
                        'total' => 0,
                    ]
                ], 404);
            }
        }

    }

    function kelurahan($no_prop = '', $no_kab = '', $no_kec = '', $no_kel = '')
    {
        $data = DB::table('m_kelurahan');
        if (empty($no_prop) || empty($no_kab) || empty($no_kec)) {
            return response()->json([
                'code' => 400,
                'status' => false,
                'data' => NULL,
            ], 400);
        } else {
            if (!empty($no_prop)) $data->where('no_propinsi', $no_prop);
            if (!empty($no_kab)) $data->where('no_kabupaten', $no_kab);
            if (!empty($no_kec)) $data->where('no_kecamatan', $no_kec);
            if (!empty($no_kel)) $data->where('no', $no_kel);
            
            $data = $data->get();
    
            if (count($data) > 0) {
                return response()->json([
                    'code' => 200,
                    'status' => true,
                    'data' => [
                        'list' => $data,
                        'total' => count($data),
                    ],
                ], 200);
            } else {
                 return response()->json([
                    'code' => 404,
                    'status' => false,
                    'data' => [
                        'list' => [],
                        'total' => 0,
                    ]
                ], 404);
            }
        }
    }
}
