<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AccountsModel extends Model
{
	use HasFactory;

	protected $table = "c_users";
	protected $fillable = ["user_group_id", "name", "email", "email_verified_at", "password", "phone_code", "phone_dial_code", "phone", "photo", "is_active"];

	function __construct()
	{
		
	}
	
	function _query($start, $limit, $search)
	{
		$query = DB::table($this->table, "u");
		$query->select(
			"u.id",
			"u.user_group_id",
			"u.name",
			"u.email",
			"u.phone",
			"u.photo",
			"u.photo_ext",
			"u.created_at",
			"u.is_active",
			"ug.name as user_group",
		);
		$query->join("c_user_groups as ug", "ug.id", "=", "u.user_group_id");
		$query->orderBy("u.created_at")->orderBy("u.name");
		// condition
		$keyword = isset($search["keyword"]) && $search["keyword"] !== "" ? $search["keyword"] : NULL;
		if ($keyword) {
			$query->where("u.name", "like", "%$keyword%");
			$query->orWhere("u.email", "like", "%$keyword%");
			$query->orWhere("ug.name", "like", "%$keyword%");
		}
		if ($limit !== 0) $query->offset($start)->limit($limit);
		$data = $query->get();
		if ($data) {
			foreach ($data as $i => $value) {
				$path_photo = 'images/accounts/avatars/';
				$data[$i]->url_photo = NULL;
				if ($value->photo && Storage::disk('public')->exists($path_photo.$value->photo)) {
					$data[$i]->url_photo = asset('storage/'.$path_photo.$value->photo);
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
