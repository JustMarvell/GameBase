@extends('layouts.admin')

@section('title', 'Edit Platform')

@section('content')
    <div class="card max-w-2xl">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.platforms.update', $platform) }}">
                @method('PUT')
                @include('admin.platforms._form')
            </form>
        </div>
    </div>
@endsection