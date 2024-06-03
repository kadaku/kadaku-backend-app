<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CouponsModel extends Model
{
	use HasFactory;

	protected $table = "m_coupons";
	protected $fillable = ["name", "description", "code", "periode_start", "periode_end", "amount", "minimum_amount", "is_private", "is_showing", "thumbnail", "is_active", "user_id"];

	function _query($start, $limit, $search)
	{
		$query = DB::table($this->table, "a");
		$query->select(
			"a.*",
			"b.name as created_by"
		);
		$query->leftJoin("c_users as b", "b.id", "=", "a.user_id");
		$query->orderBy("a.created_at");
		// condition
		$keyword = isset($search["keyword"]) && $search["keyword"] !== "" ? $search["keyword"] : NULL;
		if ($keyword) {
			$query->where("a.name", "like", "%$keyword%");
			$query->orWhere("a.code", "like", "%$keyword%");
		}
		if ($limit !== 0) $query->offset($start)->limit($limit);
		$data = $query->get();
		if ($data) {
			foreach ($data as $i => $value) {
				$path_thumbnail = 'images/coupons/thumbnails/';
				$data[$i]->url_thumbnail = NULL;
				if ($value->thumbnail && Storage::disk('public')->exists($path_thumbnail.$value->thumbnail)) {
					$data[$i]->url_thumbnail = asset('storage/'.$path_thumbnail.$value->thumbnail);
				}
			}
		}
		return $data;
	}

	function list_data($start, $limit, $search)
	{
		$data["list"] = $this->_query($start, $limit, $search);
		$data["total"] = $this->_query($start, $limit, $search)->count();
		return $data;
	}
}
