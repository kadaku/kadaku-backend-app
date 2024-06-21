<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BankAccountsModel extends Model
{
	use HasFactory;
	
	protected $table = "m_bank_accounts";
	protected $fillable = [
		"name",
		"code",
		"method",
		"account_name",
		"account_number",
		"logo",
		"is_invoice",
		"is_digital_envelope",
		"is_automatic_verification",
		"is_manual_verification",
		"is_active",
	];

	function _query($start, $limit, $search)
	{
		$query = DB::table($this->table, "a");
		$query->select(
			"a.*",
		);
		$query->orderBy("a.name", "asc");
		// condition
		$keyword = isset($search["keyword"]) && $search["keyword"] !== "" ? $search["keyword"] : NULL;
		if ($keyword) {
			$query->where("a.name", "like", "%$keyword%");
		}
		if ($limit !== 0) $query->offset($start)->limit($limit);
		$data = $query->get();
		if ($data) {
			foreach ($data as $i => $value) {
				$path = 'images/banks/';
				$data[$i]->url_logo = NULL;
				if ($value->logo && Storage::disk('public')->exists($path . $value->logo)) {
					$data[$i]->url_logo = asset('storage/' . $path . $value->logo);
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
