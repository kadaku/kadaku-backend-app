<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddonsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('m_addons')->insert(
            [
                [
                    'name' => 'Jasa Admin',
                    'description' => 'Untuk Kamu yang gak punya waktu & males bikin sendiri terima beres dibuatkan Admin',
                    'price' => 50000,
                    'discount' => 0,
                ],
                [
                    'name' => 'Wedding Filter',
                    'description' => 'Meriahkan acara dengan Instagram Wedding Filter',
                    'price' => 100000,
                    'discount' => 10,
                ],
            ]
        );
    }
}
