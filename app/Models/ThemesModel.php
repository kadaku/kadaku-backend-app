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
		);
		$query->join("m_categories as b", "b.id", "=", "a.category_id");
		$query->join("m_themes_type as c", "c.id", "=", "a.type_id");
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
		if ($data) {
			foreach ($data as $i => $value) {
				$path_thumbnail = 'images/themes/thumbnails/';
				$path_background = 'images/themes/backgrounds/';
				$data[$i]->url_background = NULL;
				if ($value->background && Storage::disk('public')->exists($path_background . $value->background)) {
					$data[$i]->url_background = asset('storage/' . $path_background . $value->background);
				}
				$data[$i]->url_thumbnail = NULL;
				if ($value->thumbnail && Storage::disk('public')->exists($path_thumbnail . $value->thumbnail)) {
					$data[$i]->url_thumbnail = asset('storage/' . $path_thumbnail . $value->thumbnail);
				}
				$data[$i]->url_thumbnail_xs = NULL;
				if ($value->thumbnail_xs && Storage::disk('public')->exists($path_thumbnail . $value->thumbnail_xs)) {
					$data[$i]->url_thumbnail_xs = asset('storage/' . $path_thumbnail . $value->thumbnail_xs);
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
