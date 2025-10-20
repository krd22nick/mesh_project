<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessExcelChunk;
use Illuminate\Support\Facades\Cache;
//use Illuminate\Support\Facades\Redis;
use Shuchkin\SimpleXLSX;

class RowsImport
{
    protected $fileKey;

    public function __construct($fileKey)
    {
        $this->fileKey = $fileKey;
    }

    public function import($filePath)
    {
        // Парсинг Excel-файла с помощью SimpleXLSX
        if ($xlsx = SimpleXLSX::parse($filePath)) {
            $rows = $xlsx->rows();
            $header = array_shift($rows); // Удаляем заголовок
            $totalRows = count($rows);

            // Сохранение общего количества строк в Redis
            Cache::set($this->fileKey . ':total_rows', $totalRows);

            // Разбиение на чанки по 1000 строк
            $chunks = array_chunk($rows, 1000);
            foreach ($chunks as $chunk) {
                ProcessExcelChunk::dispatch($chunk, $this->fileKey)->onQueue('default');
            }
        } else {
            throw new \Exception(SimpleXLSX::parseError());
        }
    }
}
