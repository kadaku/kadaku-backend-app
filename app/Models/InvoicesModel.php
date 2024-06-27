<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InvoicesModel extends Model
{
	use HasFactory;

	protected $table = "t_payment_invoices";
	protected $guarded = [];

	function __construct()
	{

	}

	function _query($start, $limit, $search)
	{
		$query = DB::table($this->table, "a");
		$query->select("a.*");
		$query->orderBy("a.created_at", "desc");
		// condition
		$keyword = isset($search["keyword"]) && $search["keyword"] !== "" ? $search["keyword"] : NULL;
		if ($keyword) {
			$query->where("a.external_id", "like", "%$keyword%");
			$query->or_where("a.payer_email", "like", "%$keyword%");
		}
		if ($limit !== 0) $query->offset($start)->limit($limit);
		$data = $query->get();
		if ($data) {
			foreach ($data as $i => $value) {
				$data[$i]->payment_method_invoice = strtoupper($value->payment_method_invoice);
			
				if ($value->created) {
					$date_created = Carbon::createFromFormat('Y-m-d\TH:i:s.v\Z', $value->created);
					// convert to desired timezone and format
					$data[$i]->created = $date_created->setTimezone('Asia/Jakarta')->format('l, d F Y H:i');
					$data[$i]->created_origin = $date_created->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s');
				}

				if ($value->reminder_date) {
					$date_reminder = Carbon::createFromFormat('Y-m-d\TH:i:s.v\Z', $value->reminder_date);
					$date_reminder = $date_reminder->copy()->addHours(8);

					// convert to desired timezone and format
					$data[$i]->reminder_date = $date_reminder->setTimezone('Asia/Jakarta')->format('l, d F Y H:i');
					$data[$i]->reminder_date_origin = $date_reminder->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s');
					
					$time_now = strtotime(date('Y-m-d H:i:s'));
					$expired_date = strtotime($data[$i]->reminder_date_origin);
					if (($time_now > $expired_date) && $data[$i]->status != 'PAID') {
						$data[$i]->status = 'EXPIRED';
					}
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
