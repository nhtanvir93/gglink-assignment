<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        DB::statement('TRUNCATE TABLE groups');

        DB::table('groups')->insert([
            [
                'name' => 'Default',
                'created_at' => now()
            ],
            [
                'name' => 'api_user_add',
                'created_at' => now()
            ],
            [
                'name' => 'api_user_delete',
                'created_at' => now()
            ],
            [
                'name' => 'api_user_detail',
                'created_at' => now()
            ],
            [
                'name' => 'api_user_all',
                'created_at' => now()
            ],
            [
                'name' => 'api_user_profile',
                'created_at' => now()
            ],
            [
                'name' => 'api_user_update',
                'created_at' => now()
            ]
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
