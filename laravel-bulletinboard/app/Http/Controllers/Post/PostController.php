<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\PostCreateRequest;
use App\Http\Requests\PostEditRequest;
use App\Contracts\Services\Post\PostServiceInterface;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PostsExport;
use App\Http\Requests\PostCsvRequest;
use App\Imports\PostsImport;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{


    private $postInterface;
    public $filteredData;
    public function __construct(PostServiceInterface $postServiceInterface)
    {
        $this->postInterface = $postServiceInterface;
    }
    //show all posts
    public function index(Request $request)
    {
        session()->forget('filteredPostList');
        $postList = $this->postInterface->getAllPosts($request);
        return view('post.list', compact('postList'));
    }

    //show post create form
    public function showPostCreate()
    {
        return view('post.create');
    }

    //submit post create result
    public function submitPostCreate(PostCreateRequest $request)
    {
        $result = $request->validated();
        return redirect()
            ->route('postconfirm')
            ->withInput();
    }

    //show post confirm form
    public function showPostConfirm()
    {
        if (old()) {
            return view('post.post-confirm');
        }
        return redirect()->route('postlist');
    }

    //submit post confirm form
    public function submitPostConfirm(Request $request)
    {
        try {
            $this->postInterface->savePost($request);
            Toastr::success('Post added successfully');
            return redirect()->route('postlist');
        } catch (\Exception $e) {
            Toastr::error('An error occurred while saving the post');
            return redirect()->route('postlist')->with('error', 'An error occurred while saving the post');
        }
    }

    //show post edit form
    public function showPostEdit($id)
    {
        $post = $this->postInterface->getPostById($id);
        return view('post.edit', compact('post'));
    }

    //submit post edit result
    public function submitPostEdit(PostEditRequest $request, $id)
    {
        $result = $request->validated();
        return redirect()
            ->route('posteditconfirm', [$id])
            ->withInput();
    }

    //show post edit confirm form
    public function showPostEditConfirm($id)
    {
        if (old()) {
            return view('post.post-edit-confirm');
        }
        return redirect()->route('postlist', [$id]);
    }

    //submit post edit confirm form
    public function submitPostEditConfirm(Request $request, $id)
    {
        try {
            $post = $this->postInterface->updatePostById($request, $id);
            Toastr::success('Post updated successfully');
            return redirect()->route('postlist');
        } catch (\Exception $e) {
            Toastr::error('An error occurred while updating the post');
            return redirect()->route('postlist')->with('error', 'An error occurred while updating the post');
        }
    }

    //delete post
    public function deletePost(Request $request)
    {
        try {
            $this->postInterface->deletePost($request);
            Toastr::success('post deleted successfully');
            return redirect()->route('postlist');
        } catch (\Exception $e) {
            Toastr::error('An error occurred while deleting the post');
            return redirect()->route('postlist')->with('error', 'An error occurred while deleting the post');
        }
    }

    //search post
    public function searchPost(Request $request)
    {
        $postList = $this->postInterface->searchPost($request);
        $filterPostList = $this->postInterface->getPostsToDownload($request);
        session(['filteredPostList' => $filterPostList]);
        return view('post.list', compact('postList'));
    }

    public function downloadPostCSV(Request $request)
    {
        try {
            if (session('filteredPostList')) {
                $postList =  session('filteredPostList');
            } else {
                $postList = $this->postInterface->getPostsToDownload($request);
            }
            $downloadFile = time() . '_posts.csv';
            return Excel::download(new PostsExport($postList), $downloadFile);
        } catch (\Exception $e) {
            Toastr::error('An error occurred while downloading the post');
            return redirect()->route('postlist');
        }
    }

    public function showPostUpload()
    {
        return view('post.upload');
    }

    public function submitPostUpload(PostCsvRequest $request)
    {
        
        $validator = Validator::make($request->all(), $request->rules());
        if ($validator->fails()) {
            return redirect()->route('postlist')
                ->withErrors($validator)
                ->withInput();
        }
        // Read the uploaded CSV file
        $csv = array_map('str_getcsv', file($request->file('csv_file')->getRealPath()));

        $requiredHeaders = ['title', 'description', 'status'];

        // Initialize an array to store missing header names
        $missingHeaders = [];
    
        // Ensure that the CSV headers contain the required columns
        $headers = array_map('strtolower', $csv[0]);
        
        foreach ($requiredHeaders as $header) {
            if (!in_array($header, $headers)) {
                $missingHeaders[] = ucfirst($header);
            }
        }
    
        if (!empty($missingHeaders)) {
            $missingHeadersString = implode(' and ', $missingHeaders);
            Toastr::error($missingHeadersString . ' is required in CSV header.');
            return view('post.upload');
        }
        // Calculate the column count (assuming the first row contains headers)
        $columnCount = count($csv[0]);
        if ($columnCount < 3 || $columnCount > 3) {
            Toastr::error('Column count of CSV must be 3');
            return view('post.upload');
        }
        try {
           
            $file = $request['csv_file'];
            Excel::import(new PostsImport, $file);
            Toastr::success("Uploading successfully");
            return redirect()->route('postlist');
        } catch (\Exception $e) {
            Toastr::error('Uploading failed' . $e->getMessage());
            return view('post.upload');
        }
    }
}
