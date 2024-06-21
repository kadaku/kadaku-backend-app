<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BankAccountsController extends Controller
{
	function list(Request $request)
	{
		$search = [
			'keyword' => $request->q,
			'for' => $request->for,
			'type' => $request->type,
		];

		$query = DB::table("m_bank_accounts", "a");
		$query->select(
			"a.id",
			"a.name",
			"a.code",
			"a.logo",
		);
		$query->where("a.is_active", "=", 1);
		$query->orderBy("a.id", "asc");
		
		// condition
		$keyword = isset($search["keyword"]) && $search["keyword"] !== "" ? $search["keyword"] : NULL;
		if ($keyword) $query->where("a.name", "like", "%$keyword%");

		$for = isset($search["for"]) && $search["for"] !== "" ? $search["for"] : NULL;
		if ($for == 'invoice') {
			$query->where("a.is_invoice", "=", 1);
		} else {
			$query->where("a.is_digital_envelope", "=", 1);
		}

		$type = isset($search["type"]) && $search["type"] !== "" ? strtolower($search["type"]) : NULL;
		if ($type == 'auto') {
			$query->where("a.is_automatic_verification", "=", 1);
			$query->where("a.is_manual_verification", "=", 0);
		} else if ($type == 'manual') {
			$query->where("a.is_manual_verification", "=", 1);
		}

		$data = $query->get();
		if (count($data) > 0) {
			foreach ($data as $i => $value) {
				$path = 'images/banks/';
				if ($value->logo && Storage::disk('public')->exists($path . $value->logo)) {
					$data[$i]->logo = asset('storage/' . $path . $value->logo);
				} else {
					$data[$i]->logo = NULL;
				}
			}

			return response()->json([
				'code' => 200,
				'status' => true,
				'data' => [
					'list' => $data,
					'total' => count($data),
				],
			], 200);
		} else {
			return response()->json([
				'code' => 404,
				'status' => false,
				'data' => [
					'list' => [],
					'total' => 0,
				]
			], 404);
		}
	}
}
