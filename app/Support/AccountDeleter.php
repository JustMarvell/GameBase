<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AccountDeleter
{
    /** Admin accounts can't be deleted from the interface (prevents locking everyone out). */
    public static function canDelete(User $user): bool
    {
        return !$user->hasRole('admin');
    }

    public static function delete(User $user): void
    {
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        DB::transaction(function () use ($user) {
            // Sessions of this user on other devices (only if sessions are stored in the database)
            if (config('session.driver') === 'database') {
                DB::table(config('session.table', 'sessions'))->where('user_id', $user->id)->delete();
            }

            // Reviews, library entries and role links are removed along with the user.
            // Games they developed stay; their developer_id becomes null (set in the earlier migration).
            $user->delete();
        });
    }
}