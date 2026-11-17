<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlatformController extends Controller
{
    public function index()
    {
        $platforms = Platform::withCount('games')->orderBy('name')->paginate(10);

        return view('admin.platforms.index', compact('platforms'));
    }

    public function create()
    {
        return view('admin.platforms.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'max:100', 'unique:platforms,name'],
        ]);

        Platform::create($data);

        return redirect()->route('admin.platforms.index')
            ->with('success', 'Platform berhasil ditambahkan.');
    }

    public function edit(Platform $platform)
    {
        return view('admin.platforms.edit', compact('platform'));
    }

    public function update(Request $request, Platform $platform)
    {
        $data = $request->validate([
            'name' => ['required', 'max:100', Rule::unique('platforms', 'name')->ignore($platform->id)],
        ]);

        $platform->update($data);

        return redirect()->route('admin.platforms.index')
            ->with('success', 'Platform berhasil diperbarui.');
    }

    public function destroy(Platform $platform)
    {
        $platform->delete(); // pivot rows are removed automatically (cascade)

        return redirect()->route('admin.platforms.index')
            ->with('success', 'Platform berhasil dihapus.');
    }
}