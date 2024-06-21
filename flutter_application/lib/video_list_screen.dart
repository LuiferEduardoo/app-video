import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'video_provider.dart';
import 'video_model.dart';
import 'edit_video_screen.dart';
import 'video_player_screen.dart';

class VideoListScreen extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    final videoProvider = Provider.of<VideoProvider>(context, listen: false);

    return Scaffold(
      appBar: AppBar(title: Text('Lista de Videos')),
      body: FutureBuilder(
        future: videoProvider.fetchVideos(),
        builder: (ctx, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return Center(child: CircularProgressIndicator());
          } else if (snapshot.error != null) {
            return Center(child: Text('Error al cargar videos: ${snapshot.error}'));
          } else {
            return Consumer<VideoProvider>(
              builder: (context, videoProvider, _) => ListView.builder(
                itemCount: videoProvider.videos.length,
                itemBuilder: (context, i) => ListTile(
                  title: Text(videoProvider.videos[i].title),
                  trailing: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      IconButton(
                        icon: Icon(Icons.edit),
                        onPressed: () {
                          Navigator.of(context).pushNamed(
                            EditVideoScreen.routeName,
                            arguments: videoProvider.videos[i],
                          );
                        },
                      ),
                      IconButton(
                        icon: Icon(Icons.delete),
                        onPressed: () {
                          videoProvider.deleteVideo(videoProvider.videos[i].id);
                        },
                      ),
                      VideoPlayerScreen(videoProvider.videos[i].id),
                    ],
                  ),
                ),
              ),
            );
          }
        },
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () {
          Navigator.of(context).pushNamed(EditVideoScreen.routeName);
        },
        child: Icon(Icons.add),
      ),
    );
  }
}
