@extends('layouts.app')

@section('title', 'Akses ditolak')

@section('content')
    <x-error-page code="403" title="Akses ditolak" message="Anda tidak memiliki izin untuk membuka halaman ini." />
@endsection