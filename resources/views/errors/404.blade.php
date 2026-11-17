@extends('layouts.app')

@section('title', 'Tidak ditemukan')

@section('content')
    <x-error-page code="404" title="Halaman tidak ditemukan"
        message="Halaman yang Anda cari tidak ada atau sudah dipindahkan." />
@endsection