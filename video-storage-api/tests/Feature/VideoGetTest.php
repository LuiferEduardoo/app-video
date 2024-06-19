<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Video;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class VideoGetTest extends TestCase
{
    use RefreshDatabase; // Reiniciar la base de datos después de cada prueba

    /** @test */
    public function test_it_can_list_all_videos()
    {
        Video::factory()->count(3)->create([
            'title' => 'Video Test',
            'description' => 'This is a test video.',
            'path' => 'This is a path'
        ]);

        $response = $this->getJson('/api/videos');

        $response->assertStatus(200)
            ->assertJsonCount(3); // Verificar que hay 3 videos en la respuesta
    }

    public function test_it_can_show_a_video()
    {
        $data = [
            'title' => 'Video Test',
            'description' => 'This is a test video.',
            'path' => 'This is a path'
        ];

        $video = Video::factory()->create($data);

        $response = $this->getJson('/api/videos/' . $video->id);

        $response->assertStatus(200)->assertJson([
            'id' => $video->id,
            'title' => $video->title,
            'description' => $video->description,
            'path' => $video->path
        ]);;
    }

    public function test_it_can_show_a_video_as_stream()
    {
        Storage::fake('public'); // Simula el almacenamiento en disco público

        // Crea un archivo falso de 1 MB
        $file = UploadedFile::fake()->create('video.mp4', 1000);

        $data = [
            'title' => 'Video Test',
            'description' => 'This is a test video.',
            'video' => $file,
        ];

        // Realiza la solicitud POST para subir el video
        $responseUploadVideo = $this->postJson('/api/videos', $data);

        $responseUploadVideo->assertStatus(201);

        $videoId = $responseUploadVideo->json('id');

        // Realiza la solicitud GET para el stream del video
        $responseStream = $this->get('/api/videos/' . $videoId . '/stream');

        // Verifica que la solicitud para el stream es exitosa
        $responseStream->assertStatus(200);

        // Verifica que la respuesta contiene los encabezados correctos para el stream de video
        $responseStream->assertHeader('Content-Type', 'video/mp4');
    }
}