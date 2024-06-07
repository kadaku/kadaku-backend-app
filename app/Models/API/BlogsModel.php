<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BlogsModel extends Model
{
	use HasFactory;
	protected $table = "t_blogs";
	protected $path;

	function __construct()
	{
		parent::__construct();
		$this->path = 'images/blogs/';
	}
	

	function _query($start, $limit, $search)
	{
		$query = DB::table($this->table, "a");
		$query->select(
			"a.id",
			"a.name",
			"a.slug",
			"a.intro",
			"a.content",
			"a.written_by",
			"a.tags",
			"a.featured_image",
			"a.created_at",
		);
		$query->orderBy("a.created_at", "desc");
		// condition
		$keyword = isset($search["keyword"]) && $search["keyword"] !== "" ? $search["keyword"] : NULL;
		if ($keyword) {
			$query->where("a.name", "like", "%$keyword%");
		}
		if ($limit !== 0) $query->offset($start)->limit($limit);
		$data = $query->get();
		if ($data) {
			foreach ($data as $i => $value) {
				$data[$i]->url_featured_image = NULL;
				if ($value->featured_image && Storage::disk('public')->exists($this->path . $value->featured_image)) {
					$data[$i]->url_featured_image = asset('storage/' . $this->path . $value->featured_image);
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

	function find_data($slug) 
	{
		$query = DB::table($this->table, "a");
		$query->select(
			"a.id",
			"a.name",
			"a.slug",
			"a.intro",
			"a.content",
			"a.written_by",
			"a.tags",
			"a.featured_image",
			"a.created_at",
		);
		$query->where("a.slug", "=", $slug);
		$data = $query->first();
		if ($data) {
			$data->url_featured_image = NULL;
			if ($data->featured_image && Storage::disk('public')->exists($this->path . $data->featured_image)) {
				$data->url_featured_image = asset('storage/' . $this->path . $data->featured_image);
			}
		}
		return $data;
	}
}
