<?php
  
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

if (!function_exists('pd')) {
    function pd($data = [])
    {
        echo '<pre>';
        print_r($data); die;
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