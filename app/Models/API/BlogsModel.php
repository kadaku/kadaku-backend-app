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
		$query->where("a.is_active", "=", 1);
		$query->where("a.is_publish", "=", 1);
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
				$data[$i]->created_at = datetime_indonesian($value->created_at, true);
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
		$query->where("a.is_active", "=", 1);
		$query->where("a.is_publish", "=", 1);
		$data = $query->first();
		if ($data) {
			$data->created_at = datetime_indonesian($data->created_at, true);
			$data->url_featured_image = NULL;
			if ($data->featured_image && Storage::disk('public')->exists($this->path . $data->featured_image)) {
				$data->url_featured_image = asset('storage/' . $this->path . $data->featured_image);
			}

			// find list new blog
			$list_query_new_article = DB::table($this->table, "a");
			$list_query_new_article->select(
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
			$list_query_new_article->where("a.id", "!=", $data->id);
			$list_query_new_article->where("a.is_active", "=", 1);
			$list_query_new_article->where("a.is_publish", "=", 1);
			$list_query_new_article->limit(5);
			$list_new_article = $list_query_new_article->get();
			if ($list_new_article) {
				foreach ($list_new_article as $i => $value) {
					$list_new_article[$i]->created_at = datetime_indonesian($value->created_at);
					$list_new_article[$i]->url_featured_image = NULL;
					if ($value->featured_image && Storage::disk('public')->exists($this->path . $value->featured_image)) {
						$list_new_article[$i]->url_featured_image = asset('storage/' . $this->path . $value->featured_image);
					}
				}
			}
			$data->list_new_article = $list_new_article;
		}
		return $data;
	}
}
