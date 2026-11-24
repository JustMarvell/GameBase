<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard admin.
     */
    public function index()
    {
        if (!auth()->user()->hasRole('admin')) {
            return $this->developerDashboard(auth()->user());
        }

        return view('admin.dashboard', [
            'counts' => [
                'games' => Game::count(),
                'genres' => Genre::count(),
                'reviews' => Review::count(),
                'users' => User::role('user')->count(),
            ],
            'latestReviews' => Review::with(['user', 'game'])->latest()->take(5)->get(),
            'topGames' => Game::withAvg('reviews', 'rating')
                ->has('reviews')
                ->orderByDesc('reviews_avg_rating')
                ->take(5)
                ->get(),
        ]);
    }

    private function developerDashboard(User $user)
    {
        $gameIds = $user->developedGames()->pluck('id');

        return view('admin.developer-dashboard', [
            'counts' => [
                'games' => $gameIds->count(),
                'reviews' => Review::whereIn('game_id', $gameIds)->count(),
                'saves' => DB::table('game_user')->whereIn('game_id', $gameIds)->count(),
                'rating' => Review::whereIn('game_id', $gameIds)->avg('rating'),
            ],
            'latestReviews' => Review::with(['user', 'game'])->whereIn('game_id', $gameIds)->latest()->take(5)->get(),
            'topGames' => $user->developedGames()->withAvg('reviews', 'rating')->has('reviews')
                ->orderByDesc('reviews_avg_rating')->take(5)->get(),
        ]);
    }
}