<?php

namespace App\Dao\Post;
use App\Contracts\Dao\Post\PostDaoInterface;
use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class PostDao implements PostDaoInterface
{

    public function savePost(Request $request)
    {  
        $post = new Post();
        $post->title = $request['title'];
        $post->description = $request['description'];
        $post->created_user_id = Auth::user()->id ?? 1;
        $post->updated_user_id = Auth::user()->id ?? 1;
        $post->save();
        return $post;
    }

    public function getAllPosts(Request $request)
    {
        $pagesize = 5;
        if($request['page_size']){
            $pagesize = $request['page_size'];
        }
        $posts = DB::table('posts as post')
        ->join('users as created_user', 'post.created_user_id', '=', 'created_user.id')
        ->join('users as updated_user', 'post.updated_user_id', '=', 'updated_user.id')
        ->select('post.*', 'created_user.name as created_user', 'updated_user.name as updated_user')
        ->where(function ($query) {
            if (Auth::guest()) {
                $query->whereNull('post.deleted_at')->where('post.status', 1);
            } elseif (Auth::user()->type == '1') {
                $id = Auth::user()->id;
                $query->whereNull('post.deleted_at')
                    ->where(function ($query) use ($id) {
                        $query->where('post.created_user_id', $id)->orWhere('post.status', 1);
                    });
            }
        })
        ->whereNull('post.deleted_at')
        ->orderBy('post.created_at', 'desc') 
        ->paginate($pagesize);
      return $posts;
    }

    public function getPostById($id){
        $post = Post::find($id);
        return $post;
    }

    public function updatePostById(Request $request, $id)
    {
        $post = Post::find($id);
        $post->title = $request['title'];
        $post->description = $request['description'];
        if ($request['status']) {
          $post->status = '1';
        } else {
          $post->status = '0';
        }
        $post->updated_user_id = Auth::user()->id;
        $post->save();
        return $post;
    }

    public function deletePost(Request $request)
    {
        $post = Post::find($request['deleteId']);
        $post->deleted_user_id = Auth::user()->id;
        $post->save();
        $post->delete();
    }

    public function searchPost(Request $request)
    {
        $pagesize = 5;
        if($request['page_size']){
            $pagesize = $request['page_size'];
        }
        $keyword = $request['keyword'];
        $posts = DB::table('posts as post')
        ->join('users as created_user', 'post.created_user_id', '=', 'created_user.id')
        ->join('users as updated_user', 'post.updated_user_id', '=', 'updated_user.id')
        ->select('post.*', 'created_user.name as created_user', 'updated_user.name as updated_user')
        ->where(function ($query) use ($keyword) {
            if (Auth::guest()) {
                $query->whereNull('post.deleted_at')->where('post.status', 1);
            } elseif (Auth::user()->type == '1') {
                $id = Auth::user()->id;
                $query->whereNull('post.deleted_at')
                    ->where(function ($query) use ($id) {
                        $query->where('post.created_user_id', $id)->orWhere('post.status', 1);
                    });
            }
        })
        ->where(function ($query) use ($keyword) {
            if ($keyword) {
                $query->where('post.title', 'like', "%$keyword%")
                    ->orWhere('post.description', 'like', "%$keyword%");
            }
        })
        ->whereNull('post.deleted_at')
        ->orderBy('post.created_at', 'desc') 
        ->paginate($pagesize);
         return $posts;
    }
    
    public function getPostsToDownload(Request $request)
    {
        $keyword = $request['keyword'];
        $posts = DB::table('posts as post')
        ->join('users as created_user', 'post.created_user_id', '=', 'created_user.id')
        ->join('users as updated_user', 'post.updated_user_id', '=', 'updated_user.id')
        ->select('post.*', 'created_user.name as created_user', 'updated_user.name as updated_user')
        ->where(function ($query) {
            if (Auth::guest()) {
                $query->whereNull('post.deleted_at')->where('post.status', 1);
            } elseif (Auth::user()->type == '1') {
                $id = Auth::user()->id;
                $query->whereNull('post.deleted_at')
                    ->where(function ($query) use ($id) {
                        $query->where('post.created_user_id', $id)->orWhere('post.status', 1);
                    });
            }
        })
        ->where(function ($query) use ($keyword) {
            if ($keyword) {
                $query->where('post.title', 'like', "%$keyword%")
                    ->orWhere('post.description', 'like', "%$keyword%");
            }
        })
        ->whereNull('post.deleted_at')
        ->get();
        return $posts;
    }
}

?>