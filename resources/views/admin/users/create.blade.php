@extends('layouts.admin')

@section('title', 'Akun Developer')

@section('content')
    <div class="card max-w-2xl">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="label-text font-semibold">Nama developer / studio</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required class="input">
                    @error('name')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="label-text font-semibold">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required class="input">
                    @error('email')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" class="label-text font-semibold">Password</label>
                    <input id="password" type="password" name="password" required class="input">
                    @error('password')
                    <p class="mt-1 text-sm text-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="label-text font-semibold">Konfirmasi password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required class="input">
                </div>

                <div class="flex justify-end gap-2">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-text">Batal</a>
                    <button type="submit" class="btn btn-primary">Buat Akun</button>
                </div>
            </form>
        </div>
    </div>
@endsection