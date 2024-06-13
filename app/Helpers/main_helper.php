<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

if (!function_exists('pd')) {
	function pd($data = [])
	{
		echo '<pre>';
		print_r($data);
		die;
		echo '</pre>';
	}
}
if (!function_exists('brand')) {
	function brand()
	{
		$brand = DB::table('c_brand')->get()->first();
		return $brand;
	}
}
if (!function_exists('convert_date_strip')) {
	function convert_date_strip($date)
	{
		return Carbon::createFromFormat('Y-m-d', $date)->format('d-m-Y');
	}
}
if (!function_exists('convert_date_strip_to_server')) {
	function convert_date_strip_to_server($date)
	{
		return Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
	}
}
if (!function_exists('convert_timestamp_to_server')) {
	function convert_timestamp_to_server($timestamp)
	{ 
		if (!empty($timestamp)) {
			return date('Y-m-d H:i:s', strtotime($timestamp));
		} else {
			return NULL;
		}
	}
}
if (!function_exists('date_indonesian')) {
	function date_indonesian($date, $print_day = false)
	{
		$hari = array(
			1 =>    'Senin',
			'Selasa',
			'Rabu',
			'Kamis',
			'Jumat',
			'Sabtu',
			'Minggu'
		);

		$bulan = array(
			1 => 'Jan',
			'Feb',
			'Mar',
			'Apr',
			'Mei',
			'Jun',
			'Jul',
			'Agu',
			'Sep',
			'Okt',
			'Nov',
			'Des'
		);
		$split    = explode('-', $date);
		$date_indonesian = $split[2] . ' ' . $bulan[(int) $split[1]] . ' ' . $split[0];

		if ($print_day) {
			$num = date('N', strtotime($date));
			return $hari[$num] . ', ' . $date_indonesian;
		}
		return $date_indonesian;
	}
}

if (!function_exists('datetime_indonesian')) {
	function datetime_indonesian($datetime, $print_day = false, $print_time = true)
	{
		$hari = array(
			1 =>    'Senin',
			'Selasa',
			'Rabu',
			'Kamis',
			'Jumat',
			'Sabtu',
			'Minggu'
		);
	
		$bulan = array(
			1 =>   'Januari',
			'Februari',
			'Maret',
			'April',
			'Mei',
			'Juni',
			'Juli',
			'Agustus',
			'September',
			'Oktober',
			'November',
			'Desember'
		);
		$split1    = explode(' ', $datetime);
		$split_time = explode(':', $split1[1]);
		$time = $split_time[0] . ':' . $split_time[1];
		$split2    = explode('-', $split1[0]);
		$tanggal_indonesia = $split2[2] . ' ' . $bulan[(int) $split2[1]] . ' ' . $split2[0];
		if ($print_time) {
			$tanggal_indonesia = $split2[2] . ' ' . $bulan[(int) $split2[1]] . ' ' . $split2[0] . ' ' . $time . ' WIB';
		}
		if ($print_day) {
			$num = date('N', strtotime($datetime));
			return $hari[$num] . ', ' . $tanggal_indonesia;
		}
		return $tanggal_indonesia;
	}
}
if (!function_exists('currency_to_number')) {
	function currency_to_number($number)
	{
		$var = str_replace(".", "", $number);
		$data = str_replace(",", ".", $var);
		return $data;
	}
}
