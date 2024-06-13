<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MusicsModel extends Model
{
	use HasFactory;

	protected $table = "m_musics";

	function _query($start, $limit, $search)
	{
		$query = DB::table($this->table, "a");
		$query->select(
			"a.id",
			"a.name",
			"a.file",
			"a.categories",
		);
		$query->where("a.is_active", "=", 1);
		$query->orderBy("a.temp_id", "desc");
		// condition
		$keyword = isset($search["keyword"]) && $search["keyword"] !== "" ? $search["keyword"] : NULL;
		$category = isset($search["category"]) && $search["category"] !== "" ? strtolower($search["category"]) : NULL;
		
		if ($keyword) $query->where("a.name", "like", "%$keyword%");
		if ($category) $query->where("a.categories", "like", "%$category%");

		if ($limit !== 0) $query->offset($start)->limit($limit);
		$data = $query->get();
		if ($data) {
			foreach ($data as $i => $value) {
				$data[$i]->categories = ucwords($value->categories);
				$path = 'songs/';
				$data[$i]->url_file = NULL;
				if ($value->file && Storage::disk('public')->exists($path . $value->file)) {
					$data[$i]->url_file = asset('storage/' . $path . $value->file);
				}
			}
		}
		return $data;
	}

	function list_data($start, $limit, $search)
	{
		$data["list"] = $this->_query($start, $limit, $search);
		$data["total"] = $this->_query(0, 0, $search)->count();
		return $data;
	}
}
