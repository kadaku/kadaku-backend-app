<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AssetMediaModel extends Model
{
	use HasFactory;
	
	protected $table = "m_asset_media";
	protected $fillable = [
		"name",
		"description",
		"keyword",
		"file",
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

	public static function list_categories() 
	{
		$data = [
			'Avatar' => 'Avatar',
			'Background' => 'Background',
			'Bank' => 'Bank',
			'Lainnya' => 'Lainnya',
		];	
		return $data;
	}
}
