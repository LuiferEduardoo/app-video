<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Video;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class VideoUpdateTest extends TestCase
{
    use RefreshDatabase; // Reiniciar la base de datos después de cada prueba

    /** @test */
    public function test_it_can_update_a_video()
    {
        $video = Video::factory()->create([
            'title' => 'Video Test',
            'description' => 'This is a test video.',
            'path' => 'This is a path'
        ]);

        $data = [
            'title' => 'Updated Video Title',
            'description' => 'Updated description.',
        ];

        $response = $this->putJson('/api/videos/' . $video->id, $data);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $video->id,
                'title' => 'Updated Video Title',
                'description' => 'Updated description.',
            ]);

        $this->assertDatabaseHas('videos', ['id' => $video->id, 'title' => 'Updated Video Title']);
    } 
}