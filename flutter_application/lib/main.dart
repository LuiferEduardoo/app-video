import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'video_list_screen.dart';
import 'video_provider.dart';
import 'edit_video_screen.dart';

void main() {
  runApp(MyApp());
}

class MyApp extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return ChangeNotifierProvider(
      create: (ctx) => VideoProvider(),
      child: MaterialApp(
        title: 'Video App',
        theme: ThemeData(
          primarySwatch: Colors.blue,
        ),
        home: VideoListScreen(),
        routes: {
          EditVideoScreen.routeName: (ctx) => EditVideoScreen(),
        },
      ),
    );
  }
}