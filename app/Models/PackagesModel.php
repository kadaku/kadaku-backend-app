<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PackagesModel extends Model
{
	use HasFactory;

	protected $table = "m_packages";
	protected $fillable = ["name", "description", "thumbnail", "price", "discount", "is_premium", "is_reseller", "is_recommended", "valid_days", "is_active"];

	function _query($start, $limit, $search)
	{
		$query = DB::table($this->table, "a");
		$query->select("a.*");
		$query->orderBy("a.created_at")->orderBy("a.name");
		// condition
		$keyword = isset($search["keyword"]) && $search["keyword"] !== "" ? $search["keyword"] : NULL;
		if ($keyword) {
			$query->where("a.name", "like", "%$keyword%");
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