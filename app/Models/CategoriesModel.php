<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CategoriesModel extends Model
{
	use HasFactory;

	protected $table = "m_categories";
	protected $fillable = ["name", "slug", "icon", "meta_title", "meta_description", "is_active"];

	function _query($start, $limit, $search)
	{
		$query = DB::table($this->table, "a");
		$query->select("a.*");
		$query->orderBy("a.created_at")->orderBy("a.name");
		// condition
		$keyword = isset($search["keyword"]) && $search["keyword"] !== "" ? $search["keyword"] : NULL;
		if ($keyword) {
			$query->where("a.name", "like", "%$keyword%");
			$query->orWhere("a.meta_title", "like", "%$keyword%");
		}
		if ($limit !== 0) $query->offset($start)->limit($limit);
		$data = $query->get();
		return $data;
	}

	function list_data($start, $limit, $search)
	{
		$data["list"] = $this->_query($start, $limit, $search);
		$data["total"] = $this->_query($start, $limit, $search)->count();
		return $data;
	}
}
