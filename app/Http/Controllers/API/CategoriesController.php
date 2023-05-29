<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\API\CategoriesModel;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    function list()
    {
        $data = CategoriesModel::where('is_active', 1)->orderBy('id')->get();
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
