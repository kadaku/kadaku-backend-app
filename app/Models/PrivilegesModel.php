<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class PrivilegesModel extends Model
{
    use HasFactory;
    protected $table = "c_user_groups";
    protected $fillable = ['name'];

    private function _query($start, $limit, $search)
    {
        $keyword = isset($search["keyword"]) && $search["keyword"] !== "" ? $search["keyword"] : NULL;
        return DB::table("c_user_groups")
            ->when($keyword, function (Builder $query, string $keyword) {
                $query->where("name", "like", "%$keyword%");
            })
            ->when($start, function (Builder $query, string $start) {
                $query->offset($start);
            })
            ->when($limit, function (Builder $query, string $limit) {
                $query->limit($limit);
            })
            ->get();
    }

    function list_data($start, $limit, $search)
    {
        $data["list"] = $this->_query($start, $limit, $search);
        $data["total"] = $this->_query(0, 0, $search)->count();
        return $data;
    }

    function list_data_privileges($user_group_id)
    {
        $query = DB::table("c_menus");
        $query->where("is_active", "=", 1);
        
        // condition
        if (auth()->user()->id !== 1) $query->where("id", "!=", 3);

        $query->orderBy("sort", "asc")->orderBy("name", "asc");

        $data["all_menus"] = $query->get();

        $query = DB::table("c_menus", "m");
        $query->join("c_privilege_menus as pm", "pm.menu_id", "=", "m.id", "left");
        $query->where("m.is_active", "=", 1);
        
        // condition
        if ($user_group_id !== "") $query->where("pm.user_group_id", "=", $user_group_id);

        $query->orderBy("m.sort", "asc")->orderBy("m.name", "asc");
        $query->select("m.*", "pm.user_group_id", "pm.menu_id");
        $data["all_menus_with_group"] = $query->get();
        
        return $data;
    }
}
