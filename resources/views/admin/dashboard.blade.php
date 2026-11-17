@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')
    <div class="card bg-linear-to-r from-secondary to-primary text-white shadow-lg">
        <div class="card-body">
            <p class="text-sm text-white/80">Selamat datang kembali,</p>
            <h2 class="text-3xl font-extrabold">{{ auth()->user()->name }} 👋</h2>
            <p class="mt-2 max-w-xl text-white/90">
                Temukan berbagai game disini
                Lihat dan berikan reviewmu!
            </p>
        </div>
    </div>
    <div class="mt-6 grid gap-6 md:grid-cols-3">
        <div class="card">
            <div class="card-body">
                <div class="flex size-11 items-center justify-center rounded-xl bg-secondary/10
    text-secondary">
                    <x-icon name="user" class="size-6" />
                </div>
                <p class="mt-3 text-sm text-base-content/60">Login sebagai</p>
                <p class="text-lg font-bold">{{ auth()->user()->email }}</p>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="flex size-11 items-center justify-center rounded-xl bg-success/15
    text-success">
                    <x-icon name="check-circle" class="size-6" />
                </div>
                <p class="mt-3 text-sm text-base-content/60">Role</p>
                <p class="text-lg font-bold">
                    <span
                        class="badge badge-soft badge-success badge-lg">{{ auth()->user()->getRoleNames()->implode(', ') }}</span>
                </p>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="flex size-11 items-center justify-center rounded-xl bg-accent/20
    text-amber-600">
                    <x-icon name="calendar" class="size-6" />
                </div>
                <p class="mt-3 text-sm text-base-content/60">Hari ini</p>
                <p class="text-lg font-bold">{{ now()->translatedFormat('l, d F Y') }}</p>
            </div>
        </div>
    </div>
@endsection