<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Video;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class VideoPostTest extends TestCase
{
    use RefreshDatabase; // Reiniciar la base de datos después de cada prueba

    /** @test */
    public function test_it_can_create_a_video()
    {
        Storage::fake('public'); // Simula el almacenamiento en disco público

        $file = UploadedFile::fake()->create('video.mp4', 1000); // Crea un archivo falso de 1 MB

        $data = [
            'title' => 'Video Test',
            'description' => 'This is a test video.',
            'video' => $file, // Crear un archivo falso de 1 MB
        ];

        $response = $this->postJson('/api/videos', $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('videos', ['title' => 'Video Test']);
    }

    public function test_it_fails_when_uploading_a_non_video_file()
    {
        Storage::fake('public'); // Simula el almacenamiento en disco público

        $file = UploadedFile::fake()->create('document.pdf', 1000); // Crea un archivo falso que no es un video

        $data = [
            'title' => 'Document Test',
            'description' => 'This is a test document.',
            'video' => $file,
        ];

        $response = $this->postJson('/api/videos', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['video']);
    }

    public function test_it_fails_when_missing_required_fields()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('video.mp4', 1000);

        // Prueba sin título
        $dataWithoutTitle = [
            'description' => 'This is a test video.',
            'video' => $file,
        ];

        $response = $this->postJson('/api/videos', $dataWithoutTitle);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title']);

        $data = [
            'title' => 'Video Test',
            'description' => 'This is a test video.',
            // No se sube ningún archivo
        ];

        $response = $this->postJson('/api/videos', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['video']);
    }

    public function test_it_fails_when_file_size_exceeds_limit()
    {
        Storage::fake('public');

        // Crea un archivo falso de 15 MB
        $file = UploadedFile::fake()->create('video.mp4', 15000);

        $data = [
            'title' => 'Large Video Test',
            'description' => 'This is a test video with large size.',
            'video' => $file,
        ];

        $response = $this->postJson('/api/videos', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['video']);
    }
}