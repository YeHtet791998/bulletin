<?php

namespace App\Contracts\Dao\Post;

use Illuminate\Http\Request;
interface PostDaoInterface
{
    public function savePost(Request $request);

    public function getAllPosts(Request $request);

    public function getPostById($id);

    public function updatePostById(Request $request, $id);

    public function searchPost(Request $request);

    public function getPostsToDownload(Request $request);

    public function deletePost(Request $request);
}

?>
