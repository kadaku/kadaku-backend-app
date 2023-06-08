<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvitationCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('m_invitation_categories')->insert(
            [
                [
                    'name' => 'Wedding & Engagement',
                    'slug' => 'wedding',
                    'meta_title' => 'Undangan Pernikahan',
                    'meta_description' => 'Tanpa Mengurangi Rasa Hormat. Kami Bermaksud Mengundang Bapak/Ibu/Saudara/i, Pada Acara Pernikahan Kami',
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'name' => 'Kids & Birthday',
                    'slug' => 'birthday',
                    'meta_title' => 'Undangan Ulang Tahun',
                    'meta_description' => 'Aku Berharap Teman-Teman Dapat Hadir di Acara Pesta Ulang Tahunku',
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'name' => 'Aqiqah & Tasmiyah',
                    'slug' => 'aqiqah-dan-tasmiyah',
                    'meta_title' => 'Undangan Aqiqah & Tasmiyah',
                    'meta_description' => 'Tanpa Mengurangi Rasa Hormat. Kami Bermaksud Mengundang Bapak/Ibu/Saudara/i, Pada Acara Aqiqah & Tasmiyah Anak Kami',
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'name' => 'Aqiqah & Tasmiyah',
                    'slug' => 'aqiqah-dan-tasmiyah',
                    'meta_title' => 'Undangan Aqiqah & Tasmiyah',
                    'meta_description' => 'Tanpa Mengurangi Rasa Hormat. Kami Bermaksud Mengundang Bapak/Ibu/Saudara/i, Pada Acara Aqiqah & Tasmiyah Anak Kami',
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'name' => 'Tasyakuran Khitan',
                    'slug' => 'khitan',
                    'meta_title' => 'Undangan Khitan',
                    'meta_description' => 'Tanpa Mengurangi Rasa Hormat. Kami Bermaksud Mengundang Bapak/Ibu/Saudara/i, Pada Acara Khitan Putra Kami',
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'name' => 'Umum & Seminar',
                    'slug' => 'umum',
                    'meta_title' => 'Undangan Umum',
                    'meta_description' => 'Dear valued customer, you are invited to this event',
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'name' => 'Syukuran & Islami',
                    'slug' => 'islami',
                    'meta_title' => 'Undangan Buka Puasa Bersama',
                    'meta_description' => 'Tanpa Mengurangi Rasa Hormat. Kami Bermaksud Mengundang Bapak/Ibu/Saudara/i, Pada Buka Puasa Bersama',
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'name' => 'Party & Dinner',
                    'slug' => 'party',
                    'meta_title' => 'Party Invitation',
                    'meta_description' => 'It is a pleasure and honor for us, if you are willing to attend and give blessings to me.',
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'name' => 'School & Graduation',
                    'slug' => 'school',
                    'meta_title' => 'Graduation Party',
                    'meta_description' => 'It is a pleasure and honor for us, if you are willing to attend and give blessings to me.',
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ],
            ]
        );
    }
}
