import 'dart:io';

class Video {
  final int id;
  final String title;
  final File video;
  final String description;

  Video({
    required this.id,
    required this.title,
    required this.video,
    required this.description,
  });

  factory Video.fromJson(Map<String, dynamic> json) {
    return Video(
      id: json['id'],
      title: json['title'],
      video: File(''), // Placeholder, ya que no estamos usando una URL
      description: json['description'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'title': title,
      'description': description,
    };
  }
}