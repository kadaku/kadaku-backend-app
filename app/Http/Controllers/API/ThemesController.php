<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ThemesController extends Controller
{
    function list(Request $request)
    {
        $data = DB::table('m_themes as t');
        $data->select('t.*','c.name as category','tt.name as type');
        $data->leftJoin('m_categories as c', 'c.id', '=', 't.category_id');
        $data->leftJoin('m_themes_type as tt', 'tt.id', '=', 't.type_id');
        $data->where('t.is_active', 1);

        if (isset($request->category_id) && !empty($request->category_id)) {
            $data->where('t.category_id', $request->category_id);
        }

        if (isset($request->category_slug) && !empty($request->category_slug)) {
            if (!empty($request->category_slug) && $request->category_slug !== 'trending') {
                $category = DB::table('m_categories')->select('id')->where('slug', $request->category_slug)->first();
                $data->where('t.category_id', $category->id);
            }
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

    public function getTheme(Request $request) {
        $theme = DB::table('m_themes')
        ->leftJoin('m_categories', 'm_categories.id', 'm_themes.category_id')
        ->leftJoin('m_themes_type', 'm_themes_type.id', 'm_themes.type_id')
        ->select([
            'm_themes.id AS theme_id',
            'm_themes.category_id AS theme_category_id',
            'm_themes.type_id AS theme_type_id',
            'm_themes.name AS theme_name',
            'm_themes.slug AS theme_slug',
            'm_themes.layout AS theme_layout',
            'm_themes.description AS theme_description',
            'm_themes.background AS theme_background',
            'm_themes.thumbnail AS theme_thumbnail',
            'm_themes.thumbnail_xs AS theme_thumbnail_xs',
            'm_themes.price AS theme_price',
            'm_themes.discount AS theme_discount',
            'm_themes.is_premium AS theme_is_premium',
            'm_themes.styles AS theme_styles',
            'm_themes.version AS theme_version',
            'm_themes.is_active AS theme_is_active',
            'm_themes.created_at AS theme_created_at',
            'm_themes.updated_at AS theme_updated_at',
            'm_categories.id AS category_id',
            'm_categories.slug AS category_slug',
            'm_categories.icon AS category_icon',
            'm_categories.meta_title AS category_meta_title',
            'm_categories.meta_description AS category_meta_description',
            'm_categories.name AS category_name',
            'm_categories.is_active AS category_is_active',
            'm_categories.created_at AS category_created_at',
            'm_categories.updated_at AS category_updated_at',
            'm_themes_type.id AS theme_type_id',
            'm_themes_type.name AS theme_type_name',
            'm_themes_type.is_active AS theme_type_is_active',
            'm_themes_type.created_at AS theme_type_created_at',
            'm_themes_type.updated_at AS theme_type_updated_at'
        ])
        ->where('m_themes.slug', $request->slug)
        ->first();

        if ($theme) :
            $components = DB::table('m_theme_components')
            ->select([
                'm_theme_components.*',
                DB::raw('(ROW_NUMBER() OVER ( PARTITION BY m_theme_components.name ORDER BY m_theme_components.order )) AS row_num')
            ])
            ->where('m_theme_components.theme_id', $theme->theme_id)
            ->where('m_theme_components.invitation_id', 0)
            ->where('m_theme_components.customer_id', 0)
            ->where('m_theme_components.is_active', 1)
            ->orderBy('m_theme_components.order', 'asc')
            ->get();

            $theme->components = $components;
            
            return response()->json([
                "status" => true,
                "message" => 'The master theme components data was found',
                "data" => $theme
            ]);
        endif;
        return response()->json([
            "status" => false,
            "message" => 'The master theme components data was not found'
        ], 404);
    }

    public function updateComponent(Request $request) {

        $value = $request->field == "props" ? json_encode($request->value) : $request->value;

        $newTheme = DB::table('m_theme_components')
        ->where('theme_id', $request->theme_id)
        ->where('name', $request->name)
        ->where('ref', $request->ref)
        ->update([
            $request->field => $value
        ]);

        if ($newTheme) :
            return response()->json([
                "status" => true,
                "message" => 'Request success'
            ]);
        endif;
        return response()->json([
            "status" => false,
            "message" => 'Process failed'
        ], 404);
    }
}
