<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserProfileController extends Controller
{
    public function show(User $user)
    {
        $viewer = auth()->user();
        $isOwner = $viewer && $viewer->id === $user->id;
        $showLibrary = $user->library_public || $isOwner;

        $reviews = $user->reviews()->with('game')->latest()->paginate(5, ['*'], 'reviews');

        $libraryTotal = $showLibrary ? $user->library()->count() : null;
        $libraryGames = $showLibrary
            ? $user->library()
                ->with('genre')
                ->withAvg('reviews', 'rating')
                ->orderByPivot('created_at', 'desc')
                ->take(12)
                ->get()
            : null;

        return view('users.show', [
            'user' => $user,
            'isOwner' => $isOwner,
            'reviews' => $reviews,
            'averageRating' => $user->reviews()->avg('rating'),
            'libraryGames' => $libraryGames,
            'libraryTotal' => $libraryTotal,
        ]);
    }
}