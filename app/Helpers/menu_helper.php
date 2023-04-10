<?php

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

if (!function_exists('menu_sidebar')) {
    function menu_sidebar($user_group_id)
    {
        $menus = DB::table('c_menus', 'm')
            ->leftJoin('c_privilege_menus as pm', 'pm.menu_id', '=', 'm.id')
            ->where('m.is_active', 1)
            ->when($user_group_id, function (Builder $query, int $user_group_id) {
                $query->where('user_group_id', $user_group_id);
            })
            ->orderBy('m.sort', 'asc')
            ->orderBy('m.name', 'asc')
            ->select(
                'm.id', 
                'm.name', 
                'm.url', 
                'm.sort', 
                'm.icon', 
                'm.is_active', 
                'm.parent_id', 
                'pm.user_group_id'
            )
            ->get();
        $data = [];  
        if (count((array) $menus) > 0) {
			foreach ($menus as $menu) {
				$row['id'] = $menu->id;
				$row['name'] = $menu->name;
				$row['url'] = $menu->url;
				$row['sort'] = $menu->sort;
				$row['icon'] = $menu->icon;
				$row['parent_id'] = $menu->parent_id;
				$row['nodes'] = NULL;

				$data[] = $row;
			}
			foreach ($data as $key => &$value) {
				$output[$value['id']] = &$value;
			}
			foreach ($data as $key => &$value) {
				if ($value['parent_id'] && isset($output[$value['parent_id']])) {
					$output[$value['parent_id']]['nodes'][] = &$value;
				}
			}
			foreach ($data as $key => &$value) {
				if ($value['parent_id'] && isset($output[$value['parent_id']])) {
					unset($data[$key]);
				}
			}	
			return $data;
		}
    }
}