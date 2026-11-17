<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GenreController extends Controller
{
    public function index()
    {
        $genres = Genre::withCount('games')->orderBy('name')->paginate(10);

        return view('admin.genres.index', compact('genres'));
    }

    public function create()
    {
        return view('admin.genres.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'max:100', 'unique:genres,name'],
            'description' => ['nullable', 'string'],
        ]);

        Genre::create($data);

        return redirect()->route('admin.genres.index')
            ->with('success', 'Genre berhasil ditambahkan.');
    }

    public function edit(Genre $genre)
    {
        return view('admin.genres.edit', compact('genre'));
    }

    public function update(Request $request, Genre $genre)
    {
        $data = $request->validate([
            'name' => ['required', 'max:100', Rule::unique('genres', 'name')->ignore($genre->id)],
            'description' => ['nullable', 'string'],
        ]);

        $genre->update($data);

        return redirect()->route('admin.genres.index')
            ->with('success', 'Genre berhasil diperbarui.');
    }

    public function destroy(Genre $genre)
    {
        if ($genre->games()->exists()) {
            return back()->with('error', "Genre {$genre->name} tidak bisa dihapus karena masih memiliki game.");
        }

        $genre->delete();

        return redirect()->route('admin.genres.index')
            ->with('success', 'Genre berhasil dihapus.');
    }
}