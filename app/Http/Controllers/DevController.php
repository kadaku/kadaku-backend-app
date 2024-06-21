<?php

namespace App\Http\Controllers;

use App\Models\DevModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DevController extends Controller
{
  private $secret_key;
	private $base_url;

	public function __construct()
	{
		$this->secret_key = 'Basic ' . config('xendit.key_auth');
		$this->base_url = 'https://kadaku.id';
	}
  
	function test_callback_xendit($id)
  {
    $request_valid = Http::withHeaders([
      'Authorization' => $this->secret_key
    ])->get('https://api.xendit.co/v2/invoices/' . $id);
    $response = $request_valid->object();

    if (!isset($response->external_id)) {
      return response()->json([
        "status" => true,
        "message" => 'Considered as a test, have a good day!'
      ], 201);
    }

    // split the string by '-' delimiter
    $parts = explode('/', 'INV/KADAKU/20240621212647/PREMIUM/1/XM1MPISNXF');
    // extract the Auth::user()->id from the parts array
    $userId = $parts[count($parts) - 2];
    // extract the context of payment from the parts array
    $context = $parts[count($parts) - 3];

    $external_id = 'INV/KADAKU/' . date('YmdHis') . '/PREMIUM/' . Auth::user()->id . '/' . strtoupper(Str::random(10));
		$external_id_hash = sha1($external_id);
    dd($external_id_hash);
  }
}
