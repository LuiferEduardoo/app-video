<?php

namespace Tests\Unit;

use Tests\TestCase; // Usa Tests\TestCase en lugar de PHPUnit\Framework\TestCase
use App\Services\VideoService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class VideoServiceTest extends TestCase
{
    public function test_upload_video(): void
    {
        Storage::fake('public'); // Simula el almacenamiento en disco público

        $file = UploadedFile::fake()->create('video.mp4', 1000); // Crea un archivo falso de 1 MB
        $path = VideoService::upload($file);

        // Verificar que el archivo se ha almacenado correctamente
        Storage::disk('public')->assertExists($path);
    }

    public function test_delete_video(): void
    {
        Storage::fake('public'); // Simula el almacenamiento en disco público

        $file = UploadedFile::fake()->create('video.mp4', 1000); // Crea un archivo falso de 1 MB
        $path = VideoService::upload($file);

        VideoService::delete($path);

        // Verificar que el archivo se ha eliminado correctamente
        Storage::disk('public')->assertMissing($path);
    }
}