<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterdataController extends Controller
{
    function list_packages(Request $request)
    {
        $data = DB::table('m_packages as p')->where('p.is_active', 1);
        if (isset($request->package_id) && !empty($request->package_id)) {
            $data->where('id', $request->package_id);
        }
        $data = $data->get();

        if (count($data) > 0) {
            foreach ($data as $i => $value) {
                $price = $value->price;
                $discount = $value->discount;
                $data[$i]->total = $price;
                if ($discount != 0) {
                    $total_discount = ($discount/100) * $price;
                    $total = $price - $total_discount;
                    $data[$i]->total = $total;
                }
            }

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
            ], 200);
        }
    }

    function list_addons()
    {
        $data = DB::table('m_addons as a')->where('a.is_active', 1)->get();
        if (count($data) > 0) {
            foreach ($data as $i => $value) {
                $price = $value->price;
                $discount = $value->discount;
                $data[$i]->total = $price;
                if ($discount != 0) {
                    $total_discount = ($discount/100) * $price;
                    $total = $price - $total_discount;
                    $data[$i]->total = $total;
                }
            }

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
            ], 200);
        }
    }
}
