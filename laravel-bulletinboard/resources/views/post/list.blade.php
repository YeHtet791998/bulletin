@extends('layouts.app')

@section('content')
<script src="{{ asset('js/moment.min.js') }}"></script>
<script src="{{ asset('js/post.js') }}"></script>
<div class="container">
{!! Toastr::message() !!}
    <div class="row">
    <div class="col-md-12">
        <div class="card">
        <div class="card-header">{{ __('Post List') }}</div>
        <div class="card-body">
          <form method="GET" action="{{ route('postsearch') }}">
            @csrf
        <div class="row mb-2 search-bar d-flex justify-content-end">
        <div class="col-md-auto">
    <label for="searchKeyword" class="col-form-label" >Keyword:</label>
  </div>
  <div class="col-md-auto">
    <input type="text" id="searchKeyword" name="keyword" class="form-control" value="{{session('last_search_keyword') }}">
  </div>
  </form>
        <div class="col-md-auto mt-xs-2">
        <button class="btn btn-primary header-btn mt-sm-1"  id="searchButton">{{ __('Search') }}</button>
        @if(auth()->user() && (auth()->user()->type == 0 || auth()->user()->type == 1))
            <a class="btn btn-primary header-btn mt-sm-1" href="/post/create">{{ __('Create') }}</a>
            <a class="btn btn-primary header-btn mt-sm-1" href="/post/upload">{{ __('Upload') }}</a>
            @endif
            <a class="btn btn-primary header-btn mt-sm-1" href="{{ route('postdownload') }}">{{ __('Download') }}</a>
            </div>
          </div>
        
      <div class="table-responsive">
        <table class="table table-hover">
            <thead>
              <tr>
                <th class="header-cell" scope="col">Post Title</th>
                <th class="header-cell" scope="col">Post Description</th>
                <th class="header-cell" scope="col">Posted User</th>
                <th class="header-cell" scope="col">Posted Date</th>
                @if(auth()->user() && (auth()->user()->type == 0 || auth()->user()->type == 1))
                <th class="header-cell" scope="col">Operation</th>
                @endif
              </tr>
            </thead>
            <tbody>
            @if ($postList->isEmpty())
                <tr class="text-center"><td colspan="5">No data available in table</td></tr>
            @else
            @foreach ($postList as $post)          
              <tr>
                <td class="align-middle">
                  <a class="post-name" style="cursor: pointer;text-decoration:none;"  data-bs-toggle="modal" data-bs-target="#detailModal" onclick="showPostDetail({{json_encode($post)}})">{{$post->title}}</a>
                <td class="align-middle">{{$post->description}}</td>
                <td class="align-middle">{{$post->created_user}}</td>
                <td class="align-middle">{{date('Y/m/d', strtotime($post->created_at))}}</td>
                @if(auth()->user() && (auth()->user()->type == 0 || auth()->user()->type == 1 && $post->created_user_id == auth()->user()->id))
                <td class="d-sm-flex gap-2">
                  <a type="button" class="btn btn-primary btn-md" href="/post/edit/{{$post->id}}">Edit</a>
                  <button type="button" class="btn btn-danger btn-md" data-bs-toggle="modal" onclick="showDeleteDetail({{json_encode($post)}})" data-bs-target="#deleteModal">Delete</button>
                </td>
                @endif
              </tr>
            @endforeach
            @endif
            </tbody>
          </table>
          </div>
          <div class="d-flex justify-content-between">
        <div class="d-flex gap-3">
      <form action="{{ route('postlist') }}" method="GET" class="form-inline justify-content-end">
    <label for="page_size">Page Size:</label>
    <select name="page_size" id="page_size"  onchange="this.form.submit()">
        <option value="5" {{ request('page_size') == 5 ? 'selected' : '' }}>5</option>
        <option value="10" {{ request('page_size') == 10 ? 'selected' : '' }}>10</option>
        <option value="15" {{ request('page_size') == 15 ? 'selected' : '' }}>15</option>
    </select>
        </form>
        <div class="text-center">
          Showing {{ $postList->firstItem() }} to {{ $postList->lastItem() }} of {{ $postList->total() }} entries
        </div>
      </div>
        <div class="d-flex justify-content-end">
        {{ $postList->appends(['page_size' => request('page_size')])->links() }}
        

        </div>
      </div>
        </div>
        </div>
    </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">{{ __('Post Detail') }}</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="post-detail">
                  <div class="col-md-12">
                    <div class="row">
                      <label class="col-md-4 text-md-left">{{ __('Title') }}</label>
                      <label class="col-md-8 text-md-left">
                        <i class="post-text" id="post-title"></i>
                      </label>
                    </div>
                    <div class="row">
                      <label class="col-md-4 text-md-left">{{ __('Description') }}</label>
                      <label class="col-md-8 text-md-left">
                        <i class="post-text" id="post-description"></i>
                      </label>
                    </div>
                    <div class="row">
                      <label class="col-md-4 text-md-left">{{ __('Status') }}</label>
                      <label class="col-md-8 text-md-left">
                        <i class="post-text" id="post-status"></i>
                      </label>
                    </div>
                    <div class="row">
                      <label class="col-md-4 text-md-left">{{ __('Created Date') }}</label>
                      <label class="col-md-8 text-md-left">
                        <i class="post-text" id="post-created-at"></i>
                      </label>
                    </div>
                    <div class="row">
                      <label class="col-md-4 text-md-left">{{ __('Created User') }}</label>
                      <label class="col-md-8 text-md-left">
                        <i class="post-text" id="post-created-user"></i>
                      </label>
                    </div>
                    <div class="row">
                      <label class="col-md-4 text-md-left">{{ __('Updated Date') }}</label>
                      <label class="col-md-8 text-md-left">
                        <i class="post-text" id="post-updated-at"></i>
                      </label>
                    </div>
                    <div class="row">
                      <label class="col-md-4 text-md-left">{{ __('Updated User') }}</label>
                      <label class="col-md-8 text-md-left">
                        <i class="post-text" id="post-updated-user"></i>
                      </label>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
</div>


<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">{{ __('Delete Confirm') }}</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="post-delete">
                  <h4 class="delete-confirm-header">Are you sure to delete post?</h4>
                  <div class="col-md-12">
                    <div class="row">
                      <label class="col-md-4 text-md-left">{{ __('ID') }}</label>
                      <label class="col-md-8 text-md-left">
                        <i class="post-text" id="post-id"></i>
                      </label>
                    </div>
                    <div class="row">
                      <label class="col-md-4 text-md-left">{{ __('Title') }}</label>
                      <label class="col-md-8 text-md-left">
                        <i class="post-text" id="post-title"></i>
                      </label>
                    </div>
                    <div class="row">
                      <label class="col-md-4 text-md-left">{{ __('Description') }}</label>
                      <label class="col-md-8 text-md-left">
                        <i class="post-text" id="post-description"></i>
                      </label>
                    </div>
                    <div class="row">
                      <label class="col-md-4 text-md-left">{{ __('Status') }}</label>
                      <label class="col-md-8 text-md-left">
                        <i class="post-text" id="post-status"></i>
                      </label>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <form action="/post/delete" method="POST">
                  <input type="hidden" name="deleteId"  id="deleteId">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-danger">Delete</button>
                </form>
                </div>
              </div>
            </div>
</div>
@endsection