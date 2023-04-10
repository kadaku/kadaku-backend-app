<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrivilegeMenusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('c_privilege_menus')->insert(
            [
                [
                    'user_group_id' => 1,
                    'menu_id' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'user_group_id' => 1,
                    'menu_id' => 2,
                    'created_at' => date('Y-m-d H:i:s')
                ],
            ]
        );
    }
}
