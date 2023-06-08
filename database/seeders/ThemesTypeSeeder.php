<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThemesTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('m_themes_type')->insert(
            [
                [
                    'name' => 'Basic',
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'name' => 'Premium',
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'name' => 'Ekslusif',
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'name' => 'Luxury',
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ],
            ]
        );
    }
}
