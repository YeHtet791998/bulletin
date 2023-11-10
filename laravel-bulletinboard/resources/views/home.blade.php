@extends('layouts.app')

@section('content')
<div class="container">
{!! Toastr::message() !!}
<h1 class="bg-danger bg-gradient shadow text-center display-3 text-shadow ">Welcome from MTM bulletinboard</h1>
</div>
@endsection
