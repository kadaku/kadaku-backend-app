<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ThemesModel extends Model
{
	use HasFactory;

	protected $table = "m_themes";
	protected $guarded = [];

	function _query($start, $limit, $search)
	{
		$query = DB::table($this->table, "a");
		$query->select(
			"a.*",
			"b.name as category",
			"c.name as type",
			"d.name as music"
		);
		$query->join("m_categories as b", "b.id", "=", "a.category_id");
		$query->join("m_themes_type as c", "c.id", "=", "a.type_id");
		$query->join("m_musics as d", "d.id", "=", "a.music_id", "left");
		$query->orderBy("a.id", "asc");
		// condition
		$keyword = isset($search["keyword"]) && $search["keyword"] !== "" ? $search["keyword"] : NULL;
		if ($keyword) {
			$query->where("a.name", "like", "%$keyword%");
			$query->orWhere("b.name", "like", "%$keyword%");
			$query->orWhere("c.name", "like", "%$keyword%");
		}
		if ($limit !== 0) $query->offset($start)->limit($limit);
		$data = $query->get();
		return $data;
	}

	function list_data($start, $limit, $search)
	{
		$data["list"] = $this->_query($start, $limit, $search);
		$data["total"] = $this->_query(0, 0, $search)->count();
		return $data;
	}
}
