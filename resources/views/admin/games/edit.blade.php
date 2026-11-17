@extends('layouts.admin')

@section('title', 'Edit Game')

@section('content')
    <div class="card max-w-3xl">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.games.update', $game) }}" enctype="multipart/form-data">
                @method('PUT')
                @include('admin.games._form')
            </form>
        </div>
    </div>
@endsection