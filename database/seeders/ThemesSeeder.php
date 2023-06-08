<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThemesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('m_themes')->insert(
            [
                [
                    'category_id' => 1,
                    'type_id' => 1,
                    'name' => 'Blue Flower',
                    'slug' => 'blue-flower',
                    'thumbnail' => 'themes/thumbnails/blue-flower',
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ]
        );
    }
}
