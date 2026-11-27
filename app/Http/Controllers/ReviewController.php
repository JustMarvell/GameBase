<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    private function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'between:1,10'],
            'body' => ['nullable', 'string', 'max:1000'],
        ];
    }

    private function messages(): array
    {
        return [
            'rating.required' => 'Pilih rating 1-10 terlebih dahulu.',
            'rating.between' => 'Rating harus antara 1 sampai 10.',
            'rating.integer' => 'Rating harus antara 1 sampai 10.',
            'body.max' => 'Ulasan maksimal 1000 karakter.',
        ];
    }

    public function store(Request $request, Game $game)
    {
        $data = $request->validate($this->rules(), $this->messages());

        if ($game->developer_id == $request->user()->id) {
            return back()->with('error', 'Anda tidak bisa mengulas game buatan sendiri.');
        }

        if ($game->reviews()->where('user_id', $request->user()->id)->exists()) {
            return back()->with('error', 'Anda sudah memberi ulasan untuk game ini.');
        }

        $game->reviews()->create($data + ['user_id' => $request->user()->id]);

        return redirect()->route('games.show', $game)
            ->with('success', 'Ulasan berhasil dikirim.');
    }

    public function update(Request $request, Review $review)
    {
        abort_unless($review->user_id === $request->user()->id, 403);

        $review->update($request->validate($this->rules(), $this->messages()));

        return redirect()->route('games.show', $review->game)
            ->with('success', 'Ulasan berhasil diperbarui.');
    }

    public function destroy(Request $request, Review $review)
    {
        $user = $request->user();

        abort_unless($review->user_id === $user->id || $user->hasRole('admin'), 403);

        $gameId = $review->game_id;
        $review->delete();

        return back()->with('success', 'Ulasan berhasil dihapus.');
    }

    public function mine(Request $request)
    {
        $reviews = $request->user()->reviews()
            ->with('genre')
            ->latest()
            ->paginate(10);

        return view('reviews.mine', compact('reviews'));
    }
}