<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BlogsModel extends Model
{
	use HasFactory;

	protected $table = "t_blogs";
	protected $fillable = [
		"name",
		"slug",
		"topic",
		"intro",
		"content",
		"source",
		"written_by",
		"featured_image",
		"hit",
		"tags",
		"is_active",
		"is_publish",
		"published_date",
		"is_delete",
		"deleted_date",
		"deleted_by",
		"created_by",
		"modiified_by",
	];

	function _query($start, $limit, $search)
	{
		$query = DB::table($this->table, "a");
		$query->select(
			"a.*",
			"b.name as created_by",
		);
		$query->leftJoin("c_users as b", "b.id", "=", "a.created_by");
		$query->orderBy("a.created_by", "desc");
		// condition
		$keyword = isset($search["keyword"]) && $search["keyword"] !== "" ? $search["keyword"] : NULL;
		if ($keyword) {
			$query->where("a.name", "like", "%$keyword%");
		}
		if ($limit !== 0) $query->offset($start)->limit($limit);
		$data = $query->get();
		if ($data) {
			foreach ($data as $i => $value) {
				$path = 'images/blogs/';
				$data[$i]->url_featured_image = NULL;
				if ($value->featured_image && Storage::disk('public')->exists($path . $value->featured_image)) {
					$data[$i]->url_featured_image = asset('storage/' . $path . $value->featured_image);
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
