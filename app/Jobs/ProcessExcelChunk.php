<?php

namespace App\Jobs;

use App\Events\RowCreated;
use App\Models\Row;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class ProcessExcelChunk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $rows;
    protected $fileKey;

    /**
     * Create a new job instance.
     */
    public function __construct(array $rows, $fileKey)
    {
        $this->rows = $rows;
        $this->fileKey = $fileKey;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        foreach ($this->rows as $row) {
            $date = Carbon::createFromFormat('d.m.Y', $row[2])->format('Y-m-d');
            $record = Row::create([
                'id' => $row[0],
                'name' => $row[1],
                'date' => $date,
            ]);

            event(new RowCreated($record));
        }

        $currentRows = Cache::get($this->fileKey . ':progress');
        Cache::set($this->fileKey . ':progress', $currentRows + count($this->rows));
    }

}
