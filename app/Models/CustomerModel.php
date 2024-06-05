<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CustomerModel extends Model
{
	use HasFactory;

	protected $table = "m_customers";

	function __construct()
	{
		$this->path_photo = 'images/customers/';
	}

	function _query($start, $limit, $search)
	{
		$query = DB::table($this->table, "c");
		$query->select(
			"c.id",
			"c.name",
			"c.email",
			"c.address",
			"c.phone",
			"c.photo",
			"c.photo_ext",
			"c.avatar",
			"c.created_at",
			"c.is_active",
		);
		$query->orderBy("c.created_at")->orderBy("c.name");
		// condition
		$keyword = isset($search["keyword"]) && $search["keyword"] !== "" ? $search["keyword"] : NULL;
		if ($keyword) {
			$query->where("c.name", "like", "%$keyword%");
			$query->orWhere("c.email", "like", "%$keyword%");
		}
		if ($limit !== 0) $query->offset($start)->limit($limit);
		$data = $query->get();
		if ($data) {
			foreach ($data as $i => $value) {
				$data[$i]->url_photo = NULL;
				if ($value->photo && Storage::disk('public')->exists($this->path_photo.base64_decode($value->photo).'.'.$value->photo_ext)) {
					$data[$i]->url_photo = asset('storage/'.$this->path_photo.base64_decode($value->photo).'.'.$value->photo_ext);
				}

				$query_social = DB::table("m_customers_social")->select("service_name")->where("customer_id", "=", $value->id)->get();
				$social = [];
				if ($query_social) {
					foreach ($query_social as $value) $social[] = ucwords($value->service_name);
				}
				$data[$i]->social = implode(", ", $social);
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
