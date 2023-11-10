<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert(
            [
              'name' => 'admin',
              'email' => 'scm.yehtetaung@gmail.com',
              'password' => Hash::make('password'),
              'profile' => '1588646773.png',
              'dob' => '2023-11-22',
              'type' => '0',
              'created_user_id' => 1,
              'updated_user_id' => 1,
            ]
          );
    }
}
