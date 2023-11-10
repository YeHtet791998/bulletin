<?php

namespace App\Imports;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Log;

class PostsImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        Log::info('uploading post');
        return new Post([
            'title'=>$row['title'],
            'description'=>$row['description'],
            'status'=>$row['status'],
            'created_user_id'=>Auth::user()->id?? $row['created_user_id'],
            'updated_user_id'=>Auth::user()->id?? $row['updated_user_id'],
        ]);
    }


}
