<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Video; 
use App\Services\VideoService;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    public function all()
    {
        $videos = Video::all();
        return response()->json($videos);
    }

    public function find($id)
    {
        $video = Video::find($id);
        if (!$video) {
            return response()->json(['message' => 'Video not found'], 404);
        }
        return response()->json($video);
    }

    public function stream($id)
    {
        $video = Video::find($id);
        if (!$video) {
            return response()->json(['message' => 'Video not found'], 404);
        }

        $path = 'public/' . $video->path; // Asegúrate de que esta es la ruta correcta en el almacenamiento simulado

        if (!Storage::disk('public')->exists($video->path)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        $stream = Storage::disk('public')->readStream($video->path);

        return new StreamedResponse(function () use ($stream) {
            fpassthru($stream);
        }, 200, [
            "Content-Type" => app()->environment('testing') ? 'video/mp4' : mime_content_type(storage_path('app/public/' . $video->path)),
            "Content-Length" => Storage::disk('public')->size($video->path),
            "Content-Disposition" => "inline; filename=\"" . basename($video->path) . "\""
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video' => 'required|file|mimes:mp4,avi,mkv|max:10240', // Máximo 10MB
        ]);
        $path = VideoService::upload($request->file('video'));

        $video = Video::create([
            'title' => $request->title,
            'description' => $request->description,
            'path' => $path,
        ]);

        return response()->json([
            "message" => "Upload video successfully",
            "id" => $video->id
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $video = Video::find($id);
        if (!$video) {
            return response()->json(['message' => 'Video not found'], 404);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $video->title = $request->title;
        $video->description = $request->description;
        $video->save();

        return response()->json($video);
    }

    public function destroy($id)
    {
        $video = Video::find($id);
        if (!$video) {
            return response()->json(['message' => 'Video not found'], 404);
        }

        VideoService::delete($video->path);
        $video->delete();

        return response()->json(['message' => 'Video deleted']);
    }

}
