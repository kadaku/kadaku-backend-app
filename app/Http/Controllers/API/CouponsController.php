<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CouponsController extends Controller
{
    function check_valid(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'coupon_code' => 'required|string|max:100',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'code' => 200,
                'status' => false,
                'data' => $validator->errors(),
            ], 200);
        }

        $data = DB::table('m_coupons')->where('code', $request->coupon_code)->where('is_active', 1);
        $data = $data->latest('id')->first();

        
        if ($data) {
            $amount = $request->amount;
            $periodeEnd = $data->periode_end;
            $today = date('Y-m-d H:i:s');

            if (strtotime($periodeEnd) <= strtotime($today)) {
                return response()->json([
                    'code' => 100,
                    'status' => false,
                    'message' => 'Coupon code is expired',
                ], 200);
            }
            
            if ($amount < $data->minimum_amount) {
                return response()->json([
                    'code' => 200,
                    'status' => false,
                    'data' => ['coupon_code' => ['Failed to use the coupon, minimum transaction Rp. ' . number_format($data->minimum_amount, 0, ',', '.')]],
                ], 200);
            }

            // cek jika sudah pernah dipakai oleh customer tersebut,
            // !! belum
            // ------------ disini ---------------
            // end cek jika sudah pernah dipakai oleh customer tersebut,

            
            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Congratulations you get a discount of Rp. ' . number_format($data->amount, 0, ',', '.'),
                'data' => [
                    'id' => $data->id,
                    'name' => 'Discount',
                    'total' => $data->amount,
                ],
            ], 200);
        } else {
            return response()->json([
                'code' => 404,
                'status' => false,
                'message' => 'Coupon code is not registered',
                'data' => $data,
            ], 200);
        }
    }

    function list()
    {
        $data = DB::table('m_coupons')->where('is_active', 1)->get();
        $data_array = [];
        if ($data) {
            foreach ($data as $i => $value) {
                if (strtotime($value->periode_end) >= strtotime(date('Y-m-d H:i:s'))) {
                    $data_array[] = [
                        'id' => $value->id,
                        'name' => $value->name,
                        'code' => $value->code,
                        'description' => $value->description,
                        'amount' => $value->amount,
                        'minimum_amount' => $value->minimum_amount,
                        'thumbnail' => $value->thumbnail,
                        'periode_end' => date_indonesian(date('Y-m-d', strtotime($value->periode_end))),
                    ];
                }
            }
        }
        if ($data_array) {
            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Coupons available',
                'data' => $data_array,
            ], 200);
        } else {
            return response()->json([
                'code' => 404,
                'status' => false,
                'message' => 'No coupons available',
                'data' => $data_array,
            ], 200);

        }
    }
}
