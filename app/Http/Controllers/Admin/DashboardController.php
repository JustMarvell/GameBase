<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard admin.
     */
    public function index()
    {
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
}