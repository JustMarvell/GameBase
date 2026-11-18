@extends('layouts.admin')

@section('title', 'Edit Game')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.games.media.index', $game) }}" class="btn btn-soft btn-secondary btn-sm">
            <x-icon name="photo" class="size-4" /> Kelola Galeri (gambar &amp; video)
        </a>
    </div>
    <div class="card max-w-3xl">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.games.update', $game) }}" enctype="multipart/form-data">
                @method('PUT')
                @include('admin.games._form')
            </form>
        </div>
    </div>
@endsection