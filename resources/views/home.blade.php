@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
    <div class="card bg-gradient-to-r from-secondary to-primary text-white shadow-lg">
        <div class="card-body">
            <h1 class="text-3xl font-extrabold">Selamat datang di {{ config('app.name') }}</h1>
            <p>Temukan dan ulas game favoritmu.</p>
        </div>
    </div>
@endsection