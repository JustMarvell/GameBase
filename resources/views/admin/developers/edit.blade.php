@extends('layouts.admin')

@section('title', 'Edit Developer')

@section('content')
    <div class="card max-w-3xl">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.developers.update', $developer) }}" enctype="multipart/form-data">
                @method('PUT')
                @include('admin.developers._form')
            </form>
        </div>
    </div>
@endsection