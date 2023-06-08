<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('m_packages')->insert(
            [
                [
                    'name' => 'Premium 1 Minggu',
                    'price' => 150000,
                    'discount' => 10,
                    'is_premium' => 1,
                    'is_recommended' => 0,
                    'valid_days' => 7,
                ],
                [
                    'name' => 'Premium 1 Bulan',
                    'price' => 300000,
                    'discount' => 10,
                    'is_premium' => 1,
                    'is_recommended' => 1,
                    'valid_days' => 30,
                ],
            ]
        );
    }
}
