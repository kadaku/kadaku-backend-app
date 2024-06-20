<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LayoutsModel extends Model
{
	use HasFactory;
	
	protected $table = "m_layouts";
	protected $fillable = [
		"title",
		"image",
		"is_active",
	];

	function _query($start, $limit, $search)
	{
		$query = DB::table($this->table, "a");
		$query->select(
			"a.*",
		);
		$query->orderBy("a.created_at", "desc");
		// condition
		$keyword = isset($search["keyword"]) && $search["keyword"] !== "" ? $search["keyword"] : NULL;
		if ($keyword) {
			$query->where("a.title", "like", "%$keyword%");
		}
		if ($limit !== 0) $query->offset($start)->limit($limit);
		$data = $query->get();
		if ($data) {
			foreach ($data as $i => $value) {
				$path = 'images/layouts/';
				$data[$i]->url_image = NULL;
				if ($value->image && Storage::disk('public')->exists($path . $value->image)) {
					$data[$i]->url_image = asset('storage/' . $path . $value->image);
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
