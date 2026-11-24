<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use App\Models\Game;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $isDeveloper = $user->hasRole('developer');

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'bio' => ['nullable', 'string', 'max:300'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];

        if ($isDeveloper) {
            $rules['website'] = ['nullable', 'url:http,https', 'max:255'];
        }

        $data = $request->validate($rules);

        // The avatar is handled separately, so an empty file input never wipes the current photo
        unset($data['avatar']);

        if ($request->hasFile('avatar')) {
            $this->deleteAvatar($user);
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        } elseif ($request->boolean('remove_avatar')) {
            $this->deleteAvatar($user);
            $data['avatar'] = null;
        }

        if (!$isDeveloper) {
            $data['library_public'] = $request->boolean('library_public');
        }

        $user->update($data);

        // Keep the developer name on their games in sync
        if ($isDeveloper && $user->wasChanged('name')) {
            Game::where('developer_id', $user->id)->update(['developer' => $user->name]);
        }

        return redirect()->route('users.show', $user)
            ->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'current_password.required' => 'Masukkan password Anda saat ini.',
            'current_password.current_password' => 'Password saat ini salah.',
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $request->user()->update(['password' => $request->password]); // hashed automatically by the model cast

        return back()->with('success', 'Password berhasil diubah.');
    }

    private function deleteAvatar($user): void
    {
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }
    }
}