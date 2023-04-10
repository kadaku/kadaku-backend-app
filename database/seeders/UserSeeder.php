<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('c_users')->insert([
            'user_group_id' => 1,
            'name' => 'Faiz Muhammad Syam',
            'email' => 'faizmsyam@gmail.com',
            'password' => '$2y$10$ni.9iPuugFBkIV4MvGz1F.pLn4RfGGo5823C3rgzRm4PHahmOZH/S',
            'phone_code' => 'ID',
            'phone_dial_code' => '62',
            'phone' => '082130050094',
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
