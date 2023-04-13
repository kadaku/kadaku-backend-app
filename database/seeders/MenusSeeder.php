<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('c_menus')->insert(
            [
                [
                    'parent_id' => NULL,
                    'name' => 'Dashboard',
                    'url' => 'dashboard',
                    'icon' => 'bx bxs-dashboard',
                    'sort' => 1,
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'parent_id' => NULL,
                    'name' => 'Management System',
                    'url' => '#',
                    'icon' => 'bx bxs-cog',
                    'sort' => 2,
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'parent_id' => 2,
                    'name' => 'Brand',
                    'url' => 'brand',
                    'icon' => 'bx bx-desktop',
                    'sort' => 1,
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'parent_id' => 2,
                    'name' => 'Admin Menus',
                    'url' => 'admin-menu',
                    'icon' => 'bx bxs-food-menu',
                    'sort' => 2,
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'parent_id' => 2,
                    'name' => 'User Accounts',
                    'url' => 'accounts',
                    'icon' => 'bx bxs-user-circle',
                    'sort' => 3,
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'parent_id' => 2,
                    'name' => 'User Group & Privileges',
                    'url' => 'privileges',
                    'icon' => 'bx bx-building-house',
                    'sort' => 4,
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ],
            ]
        );
    }
}
