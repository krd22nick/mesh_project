<?php

namespace Tests\Feature;

use App\Models\Row;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;

class ExcelControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_upload_excel_file()
    {
        Queue::fake();

        $file = UploadedFile::fake()->create('test.xlsx', 1000);

        $response = $this->postJson('/upload', [
            'file' => $file,
        ]);

        $response->assertStatus(202)
            ->assertJsonStructure(['message', 'file_key']);
    }

    public function test_get_rows_grouped_by_date()
    {
        Row::create(['id' => 1, 'name' => 'Test1', 'date' => '2023-01-01']);
        Row::create(['id' => 2, 'name' => 'Test2', 'date' => '2023-01-01']);
        Row::create(['id' => 3, 'name' => 'Test3', 'date' => '2023-01-02']);

        $response = $this->getJson('/rows');

        $response->assertStatus(200)
            ->assertJson([
                ['date' => '01.01.2023', 'names' => ['Test1', 'Test2']],
                ['date' => '02.01.2023', 'names' => ['Test3']],
            ]);
    }

    public function test_get_progress()
    {
        $fileKey = 'excel_test';
        Cache::set($fileKey . ':progress', 500);
        Cache::set($fileKey . ':total_rows', 1000);

        $response = $this->getJson('/progress?file_key=' . $fileKey);

        $response->assertStatus(200)
            ->assertJson([
                'file_key' => $fileKey,
                'progress' => 500,
                'total_rows' => 1000,
            ]);
    }

    public function test_progress_without_file_key()
    {
        $response = $this->getJson('/progress');

        $response->assertStatus(400)
            ->assertJson(['error' => 'File key is required']);
    }

}
