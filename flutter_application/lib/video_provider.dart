import 'package:flutter/material.dart';
import 'package:dio/dio.dart';
import 'dart:convert';
import 'package:file_picker/file_picker.dart';
import 'video_model.dart';

class VideoProvider with ChangeNotifier {
  List<Video> _videos = [];

  List<Video> get videos => _videos;

  Future<void> fetchVideos() async {
    final dio = Dio();
    try {
      final response = await dio.get('http://localhost:8000/api/videos');
      if (response.statusCode == 200) {
        final data = response.data as List;
        _videos = data.map((video) => Video.fromJson(video)).toList();
        notifyListeners();
      } else {
        throw Exception('Failed to load videos');
      }
    } catch (error) {
      throw Exception('Failed to load videos: $error');
    }
  }

  Future<String> getVideoStream(int id) async {
    final dio = Dio();
    try {
      final response = await dio.get(
        'http://localhost:8000/api/videos/$id/stream'
      );
      if (response.statusCode == 200) {
        return response.data; // Retorna el stream
      } else {
        throw Exception('Failed to load video stream');
      }
    } catch (error) {
      throw Exception('Failed to load video stream: $error');
    }
  }

  Future<void> addVideo(Video video) async {
    final dio = Dio();
    if (video.video == null) {
      // Manejar caso donde no se selecciona ningún video
      return;
    }

    String apiUrl = 'http://localhost:8000/api/videos';
    String fileName = video.video!.path.split('/').last; // Obtener el nombre del archivo del path

    try {
      FormData formData = FormData.fromMap({
        'video': await MultipartFile.fromFile(
          video.video!.path,
          filename: fileName,
        ),
        'title': video.title,
        'description': video.description
      });

      Response response = await dio.post(
        apiUrl,
        data: formData,
        options: Options(
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        ),
      );

      if (response.statusCode == 200) {
        // Video cargado exitosamente
        print('Video cargado exitosamente');
      } else {
        // Manejar error
        print('Error al cargar el video: ${response.data}');
      }
    } catch (e) {
      // Manejar errores de red u otros
      print('Error en la solicitud: $e');
    }
  }

  Future<void> updateVideo(Video video) async {
    final dio = Dio();
    try {
      FormData formData = FormData.fromMap({
        'title': video.title,
        'description': video.description,
      });

      final response = await dio.put(
        'http://localhost:8000/api/videos/${video.id}',
        data: formData,
      );
      if (response.statusCode == 200) {
        final index = _videos.indexWhere((v) => v.id == video.id);
        _videos[index] = Video.fromJson(response.data);
        notifyListeners();
      } else {
        throw Exception('Failed to update video');
      }
    } catch (error) {
      throw Exception('Failed to update video: $error');
    }
  }

  Future<void> deleteVideo(int id) async {
    final dio = Dio();
    try {
      final response = await dio.delete('http://localhost:8000/api/videos/$id');
      if (response.statusCode == 200) {
        _videos.removeWhere((video) => video.id == id);
        notifyListeners();
      } else {
        throw Exception('Failed to delete video');
      }
    } catch (error) {
      throw Exception('Failed to delete video: $error');
    }
  }
}