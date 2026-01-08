<?php

namespace Modules\Backup\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Inertia\Response;
use Modules\Support\Http\Controllers\BackendController;

class BackupController extends BackendController
{
    public function index(): Response
    {
        $disk = Storage::disk(config('backup.backup.destination.disks')[0]);

        $files = $disk->allFiles();

        $backups = collect($files)
            ->filter(fn ($file) => pathinfo($file, PATHINFO_EXTENSION) === 'zip')
            ->map(fn ($file) => [
                'name' => basename($file),
                'size' => number_format($disk->size($file) / 1024 / 1024, 2), // Convert to MB and format
                'date' => date('d/m/Y H:i:s', $disk->lastModified($file)),
            ])
            ->sortByDesc('date')
            ->values(); // Convert to array

        return inertia('Backup/BackupIndex', [
            'backups' => $backups,
        ]);
    }

    public function createFull(): RedirectResponse
    {
        Artisan::call('backup:run');

        return redirect()->route('backup.index')
            ->with('success', 'Full Backup created.');
    }

    public function createDB(): RedirectResponse
    {
        Artisan::call('backup:run --only-db');

        return redirect()->route('backup.index')
            ->with('success', 'Database Backup created.');
    }

    public function createFile(): RedirectResponse
    {
        Artisan::call('backup:run --only-files');

        return redirect()->route('backup.index')
            ->with('success', 'File Backup created.');
    }

    public function clean(): RedirectResponse
    {
        Artisan::call('backup:clean');

        return redirect()->route('backup.index')
            ->with('success', 'Older backups cleaned');
    }

    public function download(string $fileName)
    {
        $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
        $appBackupPath = env('APP_NAME');
        $filePath = $disk->path("{$appBackupPath}/{$fileName}");

        if (file_exists($filePath)) {
            return response()->download($filePath);
        }

        return redirect()->route('backup.index')
            ->with('error', 'File not found.');
    }

    public function destroy(string $fileName): RedirectResponse
    {
        $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
        $appBackupPath = env('APP_NAME');

        // Combine the relative path
        $relativePath = "{$appBackupPath}/{$fileName}";

        // Check if the file exists
        if ($disk->exists($relativePath)) {
            // Delete the file
            $disk->delete($relativePath);

            return redirect()->route('backup.index')
                ->with('success', 'Backup file deleted.');
        }

        return redirect()->route('backup.index')
            ->with('error', 'Backup file not found.');
    }
}
