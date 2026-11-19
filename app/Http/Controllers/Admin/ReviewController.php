<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $sorts = [
            'game' => 'games.title',
            'user' => 'users.name',
            'rating' => 'reviews.rating',
            'date' => 'reviews.created_at',
        ];
        $column = $sorts[$request->sort] ?? 'reviews.created_at';
        $dir = $request->dir === 'asc' ? 'asc' : 'desc';

        $reviews = Review::query()
            ->select('reviews.*')
            ->join('games', 'games.id', '=', 'reviews.game_id')
            ->join('users', 'users.id', '=', 'reviews.user_id')
            ->with(['game', 'user'])
            ->when($request->q, fn($query, $q) => $query->where(
                fn($w) => $w->where('games.title', 'like', "%{$q}%")
                    ->orWhere('users.name', 'like', "%{$q}%")
                    ->orWhere('reviews.body', 'like', "%{$q}%")
            ))
            ->when($request->filled('rating'), fn($query) => $query->where('reviews.rating', (int) $request->rating))
            ->when($request->text === 'with', fn($query) => $query
                ->whereNotNull('reviews.body')->where('reviews.body', '!=', ''))
            ->when($request->text === 'without', fn($query) => $query->where(
                fn($w) => $w->whereNull('reviews.body')->orWhere('reviews.body', '')
            ))
            ->orderBy($column, $dir)
            ->orderByDesc('reviews.id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('success', 'Ulasan berhasil dihapus.');
    }
}