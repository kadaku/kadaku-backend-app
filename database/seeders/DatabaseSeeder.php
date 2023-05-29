<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            BrandSeeder::class,
            MenusSeeder::class,
            UserGroupSeeder::class,
            UserSeeder::class,
            PrivilegeMenusSeeder::class,
            InvitationCategories::class,
        ]);
    }
}
