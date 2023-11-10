@extends('layouts.app')

@section('content')
<script src="{{ asset('js/moment.min.js') }}"></script>
<script src="{{ asset('js/user.js') }}"></script>
<div class="container">
  {!! Toastr::message() !!}
  <span class="visually-hidden">{{$overallIndex = ($userList->currentPage() - 1) * $userList->perPage() + 1}}</span>
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header">{{ __('User List') }}</div>
        <div class="card-body">
          <form method="GET" action="{{ route('usersearch') }}">
            @csrf
            <div class="row mb-2 search-bar d-flex justify-content-end">
              <div class="col-md-auto col-sm-3 mt-2">
                <label for="searchKeyword" class="col-form-label">Name:</label>
              </div>
              <div class="col-md-auto col-sm-9 mt-2">
                <input type="text" id="searchKeyword" name="name" class="form-control">
              </div>
              <div class="col-md-auto col-sm-3 mt-2">
                <label for="searchKeyword" class="col-form-label">Email:</label>
              </div>
              <div class="col-md-auto col-sm-9 mt-2">
                <input type="text" id="searchKeyword" name="email" class="form-control">
              </div>
              <div class="col-md-auto col-sm-3 mt-2">
                <label for="searchKeyword" class="col-form-label">From:</label>
              </div>
              <div class="col-md-auto col-sm-9 mt-2">
                <input type="date" id="searchKeyword" name="from" class="form-control">
              </div>
              <div class="col-md-auto col-sm-3 mt-2">
                <label for="searchKeyword" class="col-form-label">to:</label>
              </div>
              <div class="col-md-auto col-sm-9 mt-2">
                <input type="date" id="searchKeyword" name="to" class="form-control">
              </div>
          </form>
          <div class="col-md-auto">
            <button class="btn btn-primary header-btn mt-sm-1" id="searchButton">{{ __('Search') }}</button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th class="header-cell" scope="col">No</th>
                <th class="header-cell" scope="col">Name</th>
                <th class="header-cell" scope="col">Email</th>
                <th class="header-cell" scope="col">Created User</th>
                <th class="header-cell" scope="col">Type</th>
                <th class="header-cell" scope="col">Phone</th>
                <th class="header-cell" scope="col">Date of Birth</th>
                <th class="header-cell" scope="col">Address</th>
                <th class="header-cell" scope="col">Created_at</th>
                <th class="header-cell" scope="col">Updated_at</th>
                @if(auth()->user() && (auth()->user()->type == 0 || auth()->user()->type == 1))
                <th class="header-cell" scope="col">Operation</th>
                @endif
              </tr>
            </thead>
            <tbody>

              @if ($userList->isEmpty())
              <tr class="text-center">
                <td colspan="11">No data available in table</td>
              </tr>
              @else
              @foreach ($userList as $index => $user)
              <tr>
                <td class="align-middle">{{$overallIndex}}</td>
                <td class="align-middle">
                  <a class="user-name" style="cursor:pointer;text-decoration:none;" data-bs-toggle="modal" data-bs-target="#detailModal" onclick="showUserDetail({{json_encode($user)}})">{{$user->name}}</a>
                <td class="align-middle">{{$user->email}}</td>
                <td class="align-middle">{{$user->created_user}}</td>
                @if($user->type == 0)
                <td class="align-middle">Admin</td>
                @else
                <td class="align-middle">User</td>
                @endif
                <td class="align-middle">{{$user->phone??''}}</td>
                <td class="align-middle">{{$user->dob?date('Y/m/d', strtotime($user->dob)):''}}</td>
                <td class="align-middle">{{$user->address??''}}</td>
                <td class="align-middle">{{date('Y/m/d', strtotime($user->created_at))}}</td>
                <td class="align-middle">{{date('Y/m/d', strtotime($user->updated_at))}}</td>
                @if($user->id != auth()->user()->id)
                <td>
                  <button type="button" class="btn btn-danger view-details" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="showDeleteDetail({{json_encode($user)}})">Delete</button>
                </td>
                @endif
              </tr>
              <span class="visually-hidden">{{$overallIndex++}}</span>
              @endforeach
              @endif
            </tbody>
          </table>
        </div>
      <div class="d-flex justify-content-between">
        <div class="d-flex gap-3">
      <form action="{{ route('userlist') }}" method="GET" class="form-inline justify-content-end">
    <label for="page_size">Page Size:</label>
    <select name="page_size" id="page_size"  onchange="this.form.submit()">
        <option value="5" {{ request('page_size') == 5 ? 'selected' : '' }}>5</option>
        <option value="10" {{ request('page_size') == 10 ? 'selected' : '' }}>10</option>
        <option value="15" {{ request('page_size') == 15 ? 'selected' : '' }}>15</option>
    </select>
        </form>
        <div class="text-center">
          Showing {{ $userList->firstItem() }} to {{ $userList->lastItem() }} of {{ $userList->total() }} entries
        </div>
      </div>
        <div class="d-flex justify-content-end">
        {{ $userList->appends(['page_size' => request('page_size')])->links() }}
        </div>
      </div>

      </div>
    </div>
  </div>
</div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 800px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('User Detail') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body row" id="user-detail">
        <div class="col-md-4 ">
          <img class="img-fluid" id="user-profile" style="height: 150px; width: 150px;" src="" alt="user-profile" />
        </div>
        <div class="col-md-8">
          <div class="row">

            <label class="col-md-6 text-md-left">{{ __('Name') }}</label>
            <label class="col-md-6 text-md-left">
              <i id="user-name"></i>
            </label>
          </div>
          <div class="row">
            <label class="col-md-6 text-md-left">{{ __('Type') }}</label>
            <label class="col-md-6 text-md-left">
              <i id="user-type"></i>
            </label>
          </div>
          <div class="row">
            <label class="col-md-6 text-md-left">{{ __('Email') }}</label>
            <label class="col-md-6 text-md-left">
              <i id="user-email"></i>
            </label>
          </div>
          <div class="row">
            <label class="col-md-6 text-md-left">{{ __('Phone') }}</label>
            <label class="col-md-6 text-md-left">
              <i id="user-phone"></i>
            </label>
          </div>
          <div class="row">
            <label class="col-md-6 text-md-left">{{ __('Date of Birth') }}</label>
            <label class="col-md-6 text-md-left">
              <i id="user-date-of-birth"></i>
            </label>
          </div>
          <div class="row">
            <label class="col-md-6 text-md-left">{{ __('Address') }}</label>
            <label class="col-md-6 text-md-left">
              <i id="user-address"></i>
            </label>
          </div>
          <div class="row">
            <label class="col-md-6 text-md-left">{{ __('Created Date') }}</label>
            <label class="col-md-6 text-md-left">
              <i id="user-created-at"></i>
            </label>
          </div>
          <div class="row">
            <label class="col-md-6 text-md-left">{{ __('Created User') }}</label>
            <label class="col-md-6 text-md-left">
              <i id="user-created-user"></i>
            </label>
          </div>
          <div class="row">
            <label class="col-md-6 text-md-left">{{ __('Updated Date') }}</label>
            <label class="col-md-6 text-md-left">
              <i id="user-updated-at"></i>
            </label>
          </div>
          <div class="row">
            <label class="col-md-6 text-md-left">{{ __('Updated User') }}</label>
            <label class="col-md-6 text-md-left">
              <i id="user-updated-user"></i>
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
      <div class="modal-body" id="user-delete">
        <h4 class="delete-confirm-header">Are you sure to delete user?</h4>
        <div class="col-md-12">
          <div class="row">
            <label class="col-md-4 text-md-left">{{ __('ID') }}</label>
            <label class="col-md-8 text-md-left">
              <i id="user-id"></i>
            </label>
          </div>
          <div class="row">
            <label class="col-md-4 text-md-left">{{ __('Name') }}</label>
            <label class="col-md-8 text-md-left">
              <i id="user-name"></i>
            </label>
          </div>
          <div class="row">
            <label class="col-md-4 text-md-left">{{ __('Type') }}</label>
            <label class="col-md-8 text-md-left">
              <i id="user-type"></i>
            </label>
          </div>
          <div class="row">
            <label class="col-md-4 text-md-left">{{ __('Email') }}</label>
            <label class="col-md-8 text-md-left">
              <i id="user-email"></i>
            </label>
          </div>
          <div class="row">
            <label class="col-md-4 text-md-left">{{ __('Phone') }}</label>
            <label class="col-md-8 text-md-left">
              <i id="user-phone"></i>
            </label>
          </div>
          <div class="row">
            <label class="col-md-4 text-md-left">{{ __('Date of Birth') }}</label>
            <label class="col-md-8 text-md-left">
              <i id="user-dob"></i>
            </label>
          </div>
          <div class="row">
            <label class="col-md-4 text-md-left">{{ __('Address') }}</label>
            <label class="col-md-8 text-md-left">
              <i id="user-address"></i>
            </label>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <form action="/user/delete" method="POST">
          <input type="hidden" name="deleteId" id="deleteId">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">Delete</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection