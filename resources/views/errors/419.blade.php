@extends('layouts.app')

@section('title', 'Sesi berakhir')

@section('content')
    <x-error-page code="419" title="Sesi telah berakhir" message="Silakan muat ulang halaman lalu coba lagi." />
@endsection