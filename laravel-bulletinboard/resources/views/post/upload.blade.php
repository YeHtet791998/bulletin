@extends('layouts.app')
@section('content')
<!-- Styles -->
<link href="{{ asset('css/upload.css') }}" rel="stylesheet">
{!! Toastr::message() !!}
<div class="container">
@if ($errors->any())
@foreach ($errors->all() as $error)
    <script>
      function showToastrMessage(type, message) {
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right'
    };
    toastr[type](message);
     }
            showToastrMessage('error', '{{ $error }}');
    </script>
     @endforeach
@endif

  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">Upload CSV File</div>
        <div class="card-body">
          <form method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row mb-3">
                <div class="col-md-3 text-end ">
                <label for="csv_file" class="col-md-4 col-form-label text-md-right required">{{ __('CSV File') }}</label>
                </div>
                <div class="col-md-6">
                <input id="csv_file" type="file" class="csv-file form-control @error('csv_file') is-invalid @enderror" name="csv_file" value="{{ old('csv_file') }}" autocomplete="csv_file" autofocus>
                    <!-- @error('csv_file')
                    <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                    </span>
                    @enderror -->
                </div>
            </div>
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                <button type="submit" class="btn btn-primary" >
                  {{ __('Upload') }}
                </button>
                <button type="reset" class="btn btn-secondary">
                  {{ __('Clear') }}
                </button>
                </div>
            
            </div>
            
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection