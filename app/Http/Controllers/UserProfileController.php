<?php

namespace App\Http\Controllers;
use App\Models\Genre;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UserProfileController extends Controller
{
    public function show(User $user)
    {
        if (request()->segment(2) !== $user->slug) {
            return redirect()->route('users.show', array_merge(['user' => $user], request()->query()), 301);
        }
        
        $viewer = auth()->user();
        $isOwner = $viewer && $viewer->id === $user->id;
        $isDeveloper = $user->hasRole('developer');

        $data = [
            'user' => $user,
            'isOwner' => $isOwner,
            'isDeveloper' => $isDeveloper,
            'reviews' => $user->reviews()->with('game')->latest()->paginate(5, ['*'], 'reviews'),
            'averageRating' => $user->reviews()->avg('rating'),
            'libraryGames' => null,
            'libraryTotal' => null,
            'devGames' => null,
            'devStats' => null,
            'devGenres' => collect(),
        ];

        if ($isDeveloper) {
            // A developer's page shows the games they made; their personal library is never shown
            $gameIds = $user->developedGames()->pluck('id');

            $data['devGames'] = $user->developedGames()
                ->with('genres')
                ->withAvg('reviews', 'rating')
                ->latest()
                ->paginate(12, ['*'], 'games');

            $data['devStats'] = [
                'games' => $gameIds->count(),
                'reviews' => Review::whereIn('game_id', $gameIds)->count(),
                'rating' => Review::whereIn('game_id', $gameIds)->avg('rating'),
                'saves' => DB::table('game_user')->whereIn('game_id', $gameIds)->count(),
            ];

            $data['devGenres'] = Genre::whereHas('games', fn($q) => $q->where('games.developer_id', $user->id))
                ->orderBy('name')->get();
        } elseif ($user->library_public || $isOwner) {
            $data['libraryTotal'] = $user->library()->count();
            $data['libraryGames'] = $user->library()
                ->with('genres')
                ->withAvg('reviews', 'rating')
                ->orderByPivot('created_at', 'desc')
                ->take(12)
                ->get();
        }

        return view('users.show', $data);
    }
}