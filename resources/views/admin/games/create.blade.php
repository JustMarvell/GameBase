@extends('layouts.admin')

@section('title', 'Tambah Game')

@section('content')
    <div class="card max-w-3xl">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.games.store') }}" enctype="multipart/form-data">
                @include('admin.games._form')
            </form>
        </div>
    </div>
@endsection