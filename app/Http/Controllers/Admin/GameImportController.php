<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GameCsvImporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GameImportController extends Controller
{
    public function create()
    {
        return view('admin.games.import');
    }

    public function template()
    {
        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM, so Excel reads it as UTF-8

            fputcsv($out, [
                'title',
                'developer',
                'developer_description',
                'release_year',
                'cover_image',
                'description',
                'genre',
                'platforms',
                'images',
                'video',
            ], ',', '"', '\\');

            fputcsv($out, [
                'Contoh Game',
                'Studio Contoh',
                'Deskripsi singkat studio.',
                2024,
                'https://example.com/cover-potret.jpg',
                'Deskripsi game.',
                'Action, Adventure',
                'PC, PlayStation 5',
                'https://example.com/1.jpg | https://example.com/2.jpg',
                'https://www.youtube.com/watch?v=XXXXXXXXXXX',
            ], ',', '"', '\\');

            fclose($out);
        }, 'template-game.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'extensions:csv,txt', 'max:5120'],
            'password_mode' => ['required', 'in:pattern,random'],
        ], [
            'file.required' => 'Pilih file CSV terlebih dahulu.',
            'file.extensions' => 'File harus berformat .csv',
            'file.max' => 'Ukuran file maksimal 5MB.',
            'file.uploaded' => 'File gagal diunggah. Periksa batas upload server.',
        ]);

        $this->discardPending();

        $file = $request->file('file');

        session([
            'game_import' => [
                'path' => $file->store('imports', 'local'),
                'name' => $file->getClientOriginalName(),
                'mode' => $request->password_mode,
            ]
        ]);

        return redirect()->route('admin.games.import.preview');
    }

    public function preview(GameCsvImporter $importer)
    {
        $pending = $this->pending();

        if (!$pending) {
            return redirect()->route('admin.games.import')
                ->with('error', 'Tidak ada file impor yang menunggu. Unggah file CSV dulu.');
        }

        set_time_limit(300);

        $report = $importer->run(Storage::disk('local')->path($pending['path']), $pending['mode'], commit: false);

        return view('admin.games.import-preview', compact('report', 'pending'));
    }

    public function run(GameCsvImporter $importer)
    {
        $pending = $this->pending();

        if (!$pending) {
            return redirect()->route('admin.games.import')
                ->with('error', 'Tidak ada file impor yang menunggu. Unggah file CSV dulu.');
        }

        set_time_limit(300);

        $report = $importer->run(Storage::disk('local')->path($pending['path']), $pending['mode'], commit: true);

        $this->discardPending();

        return redirect()->route('admin.games.import')->with('import_report', $report);
    }

    public function cancel()
    {
        $this->discardPending();

        return redirect()->route('admin.games.import')->with('success', 'Impor dibatalkan.');
    }

    private function pending(): ?array
    {
        $pending = session('game_import');

        return $pending && Storage::disk('local')->exists($pending['path']) ? $pending : null;
    }

    private function discardPending(): void
    {
        if ($pending = session('game_import')) {
            Storage::disk('local')->delete($pending['path']);
        }

        session()->forget('game_import');
    }
}