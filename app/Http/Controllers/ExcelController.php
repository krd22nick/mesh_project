<?php

namespace App\Http\Controllers;

use App\Models\Row;
use Illuminate\Http\Request;
use App\Http\Controllers\RowsImport;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ExcelController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240', // Max 10MB
        ]);

        $file = $request->file('file');
        $fileKey = 'excel_' . Str::uuid();
        $filePath = storage_path('app/' . $file->storeAs('uploads', $fileKey . '.' . $file->extension()));

        $filePathFix = str_replace('uploads', 'private/uploads', $filePath);

        // Инициализация прогресса в Redis
        Cache::set($fileKey . ':progress', 0);
        Cache::set($fileKey . ':total_rows', 0);

        // Импорт файла
        try {
            $import = new RowsImport($fileKey);
            $import->import($filePathFix);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to process file: ' . $e->getMessage()], 400);
        }

        return response()->json([
            'message' => 'File uploaded and processing started',
            'file_key' => $fileKey,
        ], 202);
    }

    public function getRows()
    {
        $rows = Row::groupedByDate();

        return response()->json($rows);
    }

    public function getProgress(Request $request)
    {
        $fileKey = $request->query('file_key');
        if (!$fileKey) {
            return response()->json(['error' => 'File key is required'], 400);
        }

        $progress = Cache::get($fileKey . ':progress');
        $totalRows = Cache::get($fileKey . ':total_rows');

        return response()->json([
            'file_key' => $fileKey,
            'progress' => (int)$progress,
            'total_rows' => (int)$totalRows,
        ]);
    }

}
