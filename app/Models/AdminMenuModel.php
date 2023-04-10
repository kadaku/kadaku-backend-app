<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class AdminMenuModel extends Model
{
    use HasFactory;

    protected $table = "c_menus";

    protected $fillable = [
        'parent_id','name','url','icon','sort',
    ];

    private function _child($parent_id, $search = [])
    {
        $keyword = isset($search["keyword"]) && $search["keyword"] !== "" ? $search["keyword"] : NULL;
        return DB::table($this->table)
            ->where("parent_id", "=", $parent_id)
            ->when($keyword, function (Builder $query, string $keyword) {
                $query->where("name", "like", "%$keyword%");
            })
            ->orderBy("sort", "asc")
            ->orderBy("name", "asc")
            ->get()->all();
    }

    function list_data($start, $limit, $search = [])
    {
        $keyword = isset($search["keyword"]) && $search["keyword"] !== "" ? $search["keyword"] : NULL;
        $list_parent = DB::table($this->table)
            ->whereNull("parent_id")
            ->orderBy("sort", "asc")
            ->orderBy("name", "asc")
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
        if ($list_parent) {
            foreach ($list_parent as $key => $value) {
                $list_child = $this->_child($value->id, $search);
                // child
                if ($list_child) {
                    $list_parent[$key]->child = $list_child;
                    foreach ($list_child as $key2 => $value2) {
                        $list_child2 = $this->_child($value2->id, $search);
                        // child
                        if ($list_child2) {
                            $list_parent[$key]->child[$key2]->child = $list_child2;
                        } else {
                            $list_parent[$key]->child[$key2]->child = [];
                        }
                    }
                } else {
                    $list_parent[$key]->child = [];
                }
            }
        }
        $data["list"] = $list_parent;
        $data["total"] = DB::table($this->table)->whereNull("parent_id")->get()->count();
        return $data;
    }

    function list_data_search($start, $limit, $search = [])
    {
        $keyword = isset($search["keyword"]) && $search["keyword"] !== "" ? $search["keyword"] : NULL;
        $list = DB::table($this->table, "a")
            ->join($this->table . " as b", "b.id", "=", "a.parent_id", "left")
            ->where("a.id", "!=", null)
            ->when($keyword, function (Builder $query, string $keyword) {
                $query->where("a.name", "like", "%$keyword%")->orWhere("b.name", "like", "%$keyword%");
            })
            ->when($start, function (Builder $query, int $start) {
                $query->offset($start);
            })
            ->when($limit, function (Builder $query, int $limit) {
                $query->limit($limit);
            })
            ->get();
        $data["list"] = $list;
        $data["total"] = DB::table($this->table, "a")
                            ->join($this->table . " as b", "b.id", "=", "a.parent_id", "left")
                            ->where("a.id", "!=", null)
                            ->when($keyword, function (Builder $query, string $keyword) {
                                $query->where("a.name", "like", "%$keyword%")->orWhere("b.name", "like", "%$keyword%");
                            })
                            ->get()
                            ->count();
        return $data;
    }
}
