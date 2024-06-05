<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MusicsModel extends Model
{
	use HasFactory;

	protected $table = "m_musics";
	protected $fillable = ["category_music_id", "temp_id", "name", "file", "file_url", "categories", "is_active", "created_by", "modified_by"];

	function _query($start, $limit, $search)
	{
		$query = DB::table($this->table, "a");
		$query->select(
			"a.*",
			"b.name as created_by",
			"c.name as category_music"
		);
		$query->leftJoin("c_users as b", "b.id", "=", "a.created_by");
		$query->leftJoin("m_categories_musics as c", "c.id", "=", "a.category_music_id");
		$query->orderBy("a.temp_id", "desc");
		// condition
		$keyword = isset($search["keyword"]) && $search["keyword"] !== "" ? $search["keyword"] : NULL;
		if ($keyword) {
			$query->where("a.name", "like", "%$keyword%");
			$query->orWhere("a.categories", "like", "%$keyword%");
		}
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
