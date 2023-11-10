<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use League\Csv\Reader;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\UploadedFile;
use App\Imports\PostsImport;
use Illuminate\Support\Facades\Session;

class PostTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    // use RefreshDatabase;
    // use DatabaseTransactions;

    public function createUser($isAdmin = false)
    {
        $user = User::factory()->create([
            'name' => 'john',
            'email' => 'john@gmail.com',
            'password' => bcrypt('password'),
            'profile' => '1588646773.png',
            'type' => ($isAdmin) ? '0':'1',
            'created_user_id' => 1,
            'updated_user_id' => 1,
        ]);
        return $user;
    }

    public function test_csv_import_post()
    {
       
       User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'profile' => '1588646773.png',
            'type' => '1',
            'created_user_id' => 1,
            'updated_user_id' => 1,
        ]);
        $user = User::where('email', 'admin@gmail.com')->first();
        $filePath = storage_path('app/testbulletin.csv');

        $csvFile = new UploadedFile($filePath, 'testbulletin.csv');
        $uploadFolder = [
      'csv_file' => $csvFile,
      'created_user_id' => $user->id,
      'updated_user_id' => $user->id,
       ];
         Excel::import(new PostsImport(), $csvFile);
        $this->actingAs($user)->post('post/upload/', $uploadFolder);
        $this->assertDatabaseHas('posts', [
                'title' => 'post eight',
                'description' => 'post eight description', 
            ]);
    }
    // public function createPost($user)
    // {
    //     $post = Post::factory()->create([
    //         'title'=>'post one',
    //         'description'=>'post one description',
    //         "status"=>"1",
    //         'created_user_id'=> $user->id,
    //         'updated_user_id'=> $user->id,
    //     ]);
    //     return $post;
    // }

    public function test_post_list()
    {
        $response = $this->get('/post/list');
        $response->assertStatus(200)
        ->assertViewHas('postList');
    }
    // public function test_post_list_user()
    // {
    //     $this->createUser(false);
    //     $user = User::where('email', 'john@gmail.com')->first();
    //     $response = $this->actingAs($user)->get('/post/list');
    //     $response->assertStatus(200)
    //     ->assertViewHas('postList');
    // }

    // public function test_create_post()
    // {
    //     $this->createUser(true);
    //     $user = User::where('email', 'john@gmail.com')->first();
    //     $postData = [
    //         'title'=>'post one',
    //         'description'=>'post one description',
    //         'created_user_id'=> $user->id,
    //         'updated_user_id'=> $user->id,
    //     ];
    //     $response = $this->actingAs($user)->post('/post/create/confirm', $postData);
    //     $response->assertRedirect('/post/list');
    //     $this->assertDatabaseHas('posts', [
    //         'title' => 'post one',
    //         'description' => 'post one description',
    //     ]);
    // }

    // public function test_update_post()
    // {
    //     $this->createUser(true);
    //     $user = User::where('email', 'john@gmail.com')->first();
    //     $this->createPost($user);
    //     $post = Post::where('title', 'post one')->first();
    //     $updateData = [
    //         'title' => 'update post one',
    //         'description'=>'update post one description',
    //         'updated_user_id'=> $user->id,
    //         'status'=> 'true'
    //     ];
    //     $response = $this->actingAs($user)->post("/post/edit/{$post->id}/confirm", $updateData);
    //     $response->assertRedirect('/post/list');
    //     $updatePost = Post::find($post->id);
    //     $this->assertEquals('update post one', $updatePost->title);
    //     $this->assertEquals('update post one description', $updatePost->description);
    // }

    // public function test_delete_post()
    // {
    //     $this->createUser(true);
    //     $user = User::where('email', 'john@gmail.com')->first();
    //     $this->createPost($user);
    //     $post = Post::where('title', 'post one')->first();
    //     $deleteData = [
    //         'deleteId' => $post->id
    //     ];
    //     $this->actingAs($user)->delete("/post/delete", $deleteData);
    //     $response = $this->get('/post/list')
    //     ->assertDontSeeText('post one');
    // }

    // public function test_csv_export_post()
    // {   
    //     $this->createUser(true);
    //     $user = User::where('email', 'john@gmail.com')->first();
    //     for ($i = 1; $i <= 5; $i++) {
    //         Post::factory()->create([
    //             'title'=>"post {$i}",
    //             'description'=>"post {$i} description",
    //             "status"=>"1",
    //             'created_user_id'=> $user->id,
    //             'updated_user_id'=> $user->id,
    //         ]);
    //     }
    //     $downloadFile = time() .'_posts.csv';
    //     $response = $this->actingAs($user)->get('post/download/')
    //     ->assertStatus(200);
    // }

    // public function test_create_duplicate_post()
    // {
    //     $this->createUser(true);
    //     $user = User::where('email', 'john@gmail.com')->first();
    //     $this->createPost($user);
    //     $response = $this->actingAs($user)->post('/post/create', [
    //         'title' => 'post one',
    //         'description' => 'Another description',
    //     ]);
    //     $response->assertStatus(302);
    // }

    // public function test_post_create_view()
    // {
    //     $this->createUser(true);
    //     $user = User::where('email', 'john@gmail.com')->first();
    //     $response = $this->actingAs($user)->get('/post/create')
    //     ->assertStatus(200);
    // }
    
    // public function test_post_upload_view()
    // {
    //     $this->createUser(true);
    //     $user = User::where('email', 'john@gmail.com')->first();
    //     $response = $this->actingAs($user)->get('post/upload/')
    //     ->assertStatus(200);       
    // }

    // public function test_post_edit_request()
    // {
    //     $this->createUser(true);
    //     $user = User::where('email', 'john@gmail.com')->first();
    //     $this->createPost($user);
    //     $post = Post::where('title', 'post one')->first();
    //     $postData = [
    //         'title'=>'post one update',
    //         'description'=>'post one description',
    //     ];
    //     $response = $this->actingAs($user)->post("/post/edit/{$post->id}", $postData)
    //     ->assertStatus(302);
    // }

    // public function test_submit_post_create()
    // {
    //     $this->createUser(true);
    //     $user = User::where('email', 'john@gmail.com')->first();
    //     $postData = [
    //         'title'=>'post one',
    //         'description'=>'post one description',
    //     ];
    //     $response = $this->actingAs($user)->post("/post/create", $postData)
    //     ->assertRedirect('post/create/confirm');
    // }

    // public function test_show_post_edit()
    // {
    //     $this->createUser(true);
    //     $user = User::where('email', 'john@gmail.com')->first();
    //     $post = $this->createPost($user);
    //     $response = $this->actingAs($user)->get("/post/edit/{$post->id}")
    //     ->assertStatus(200);
    // }

    // public function test_search_post()
    // {
    //     $this->createUser(true);
    //     $user = User::where('email', 'john@gmail.com')->first();
    //     $post = $this->createPost($user);
    //     $searchData = [
    //         'keyword' => 'one'
    //     ];
    //     $response = $this->actingAs($user)->get("post/search",$searchData)
    //     ->assertViewIs('post.list')
    //     ->assertSeeText('post one');
    // }  

    // public function test_search_post_guest()
    // {
    //     $this->createUser();
    //     $user = User::where('email', 'john@gmail.com')->first();
    //     $post = $this->createPost($user);
    //     $searchData = [
    //         'keyword' => 'one'
    //     ];
    //     $response = $this->get("post/search",$searchData)
    //     ->assertViewIs('post.list')
    //     ->assertSeeText('post one');
    // } 

    // public function test_search_post_user()
    // {
    //     $this->createUser(false);
    //     $user = User::where('email', 'john@gmail.com')->first();
    //     $post = $this->createPost($user);
    //     $searchData = [
    //         'keyword' => 'one'
    //     ];
    //     $response = $this->actingAs($user)->get("post/search",$searchData)
    //     ->assertViewIs('post.list')
    //     ->assertSeeText('post one');
    // }

    // public function test_show_post_confirm()
    // {
    //     $this->createUser(true);
    //     $user = User::where('email', 'john@gmail.com')->first();
    //     session()->flashInput([ 
    //     'title'=>'post one',
    //     'description'=>'post one description']);
    //     $response = $this->actingAs($user)->get("/post/create/confirm")
    //     ->assertViewIs('post.post-confirm');
    // }

    // public function test_show_post_edit_confirm() {
    //     $this->createUser(true);
    //     $user = User::where('email', 'john@gmail.com')->first();
    //     $post = $this->createPost($user);
    //     session()->flashInput([ 
    //     'title'=>'post one',
    //     'description'=>'post one description']);
    //     $response = $this->actingAs($user)->get("/post/edit/{$post->id}/confirm")
    //     ->assertViewIs('post.post-edit-confirm');        
    // }

    // public function test_fail_submit_post_confirm() {
    //     $this->createUser(true);
    //     $user = User::where('email', 'john@gmail.com')->first();       
    //     $postData = [
    //         'title'=>null,
    //     ];
    //     $response = $this->actingAs($user)->post('/post/create/confirm', $postData)
    //     ->assertRedirect(route('postlist'))
    //     ->assertSessionHas('error', 'An error occurred while saving the post');      
    // }

    // // public function test_fail_delete_post() {
    // //     $this->createUser(true);
    // //     $user = User::where('email', 'john@gmail.com')->first();
    // //     $response = $this->actingAs($user)->delete('/post/delete', $request)
    // //     ->assertRedirect(route('postlist'))
    // //     ->assertSessionHas('error', 'An error occurred while deleting the post');      
    // // }
    // public function test_fail_update_post() {
    //     $this->createUser(true);
    //     $user = User::where('email', 'john@gmail.com')->first();
    //     $this->createPost($user);
    //     $post = Post::where('title', 'post one')->first();
    //     $updateData = [
    //         'title' => null
    //     ];
    //     $response = $this->actingAs($user)->post("/post/edit/{$post->id}/confirm", $updateData)
    //     ->assertRedirect(route('postlist'))
    //     ->assertSessionHas('error', 'An error occurred while updating the post'); 
    // }
    
    // public function test_fail_show_post_confirm()
    // {
    //     $this->createUser(true);
    //     $user = User::where('email', 'john@gmail.com')->first();
    //     $response = $this->actingAs($user)->get("/post/create/confirm")
    //     ->assertRedirect(route('postlist'));       
    // }
}
