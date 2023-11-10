<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class PostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('posts')->insert([
            [
                'title' => 'Sample Post 1',
                'description' => 'This is the first sample post.',
                'status' => 1, 
                'created_user_id' => 1, 
                'updated_user_id' => 1, 
                'deleted_user_id' => null,
            ],
        ]);
    }
}
