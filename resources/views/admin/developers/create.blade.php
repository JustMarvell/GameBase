@extends('layouts.admin')

@section('title', 'Tambah Developer')

@section('content')
    <div class="card max-w-3xl">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.developers.store') }}" enctype="multipart/form-data">
                @include('admin.developers._form')
            </form>
        </div>
    </div>
@endsection