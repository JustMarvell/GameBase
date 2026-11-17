@extends('layouts.admin')

@section('title', 'Tambah Genre')

@section('content')
    <div class="card max-w-2xl">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.genres.store') }}">
                @include('admin.genres._form')
            </form>
        </div>
    </div>
@endsection