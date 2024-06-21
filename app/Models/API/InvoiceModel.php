<?php

namespace App\Models\API;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InvoiceModel extends Model
{
	use HasFactory;
	protected $table = "t_payment_invoices";

	function _query($start, $limit, $search)
	{
		$query = DB::table($this->table, "a");
		$query->select(
			"*",
		);
		$query->orderBy("a.created_at", "desc");
		// condition
		$keyword = isset($search["keyword"]) && $search["keyword"] !== "" ? $search["keyword"] : NULL;
		
		if ($keyword) $query->where("a.name", "like", "%$keyword%");

		if ($limit !== 0) $query->offset($start)->limit($limit);
		$data = $query->get();
		if ($data) {
			foreach ($data as $i => $value) {
				$data[$i]->payment_method_invoice = strtoupper($value->payment_method_invoice);
			
				if ($value->reminder_date) {
					$date_reminder = Carbon::createFromFormat('Y-m-d\TH:i:s.v\Z', $value->reminder_date);
					// convert to desired timezone and format
					$data[$i]->reminder_date = $date_reminder->setTimezone('Asia/Jakarta')->format('l, d F Y H:i');
					$data[$i]->reminder_date_origin = $date_reminder->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s');
				}
				
				if ($value->paid_at) {
					$date_paid = Carbon::createFromFormat('Y-m-d\TH:i:s.v\Z', $value->paid_at);
					// convert to desired timezone and format
					$data[$i]->paid_at = $date_paid->setTimezone('Asia/Jakarta')->format('l, d F Y H:i');
					$data[$i]->paid_at_origin = $date_paid->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s');
				}
	
				$data[$i]->items = json_decode($value->items);
				$data[$i]->fees = json_decode($value->fees);
				$data[$i]->packages = json_decode($value->packages);
				$data[$i]->addons = json_decode($value->addons);
				$data[$i]->coupons = json_decode($value->coupons);
				$data[$i]->customer = json_decode($value->customer);
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
