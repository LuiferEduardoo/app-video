<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Video;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class VideoDeleteTest extends TestCase
{
    use RefreshDatabase; // Reiniciar la base de datos después de cada prueba

    /** @test */
    public function test_it_can_delete_a_video()
    {
        Storage::fake('public'); // Simula el almacenamiento en disco público
    
        $file = UploadedFile::fake()->create('video.mp4', 1000); // Crea un archivo falso de 1 MB
    
        $data = [
            'title' => 'Video Test',
            'description' => 'This is a test video.',
            'video' => $file,
        ];
    
        // Realiza la solicitud POST para subir el video
        $responseUploadVideo = $this->postJson('/api/videos', $data);
    
        $responseUploadVideo->assertStatus(201);
    
        $videoId = $responseUploadVideo->json('id');
    
        // Realiza la solicitud DELETE para eliminar el video usando el id obtenido
        $responseDeleteVideo = $this->deleteJson('/api/videos/' . $videoId);
    
        $responseDeleteVideo->assertStatus(200)
            ->assertJson(['message' => 'Video deleted']);
    
        // Asegúrate de que el video haya sido eliminado de la base de datos
        $this->assertDatabaseMissing('videos', ['id' => $videoId]);
    }    
}