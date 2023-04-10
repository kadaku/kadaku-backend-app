<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('c_brand')->insert([
            'name' => 'Kadaku - Plaftform Undangan Digital Online',
            'description' => 'Solusi tepat untuk undangan praktis, hemat, design kekinian dengan undangan sebar otomatis.',
            'email' => 'kadaku.official@gmail.com',
            'address' => 'Tangerang',
            'phone_code' => 'ID',
            'phone_dial_code' => '62',
            'phone' => '085966622963',
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
