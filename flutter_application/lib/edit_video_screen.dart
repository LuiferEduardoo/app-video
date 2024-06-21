import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:provider/provider.dart';
import 'dart:io';
import 'package:file_picker/file_picker.dart';
import 'package:path_provider/path_provider.dart';
import 'video_provider.dart';
import 'video_model.dart';

class EditVideoScreen extends StatefulWidget {
  static const routeName = '/edit-video';
  final Video? video;

  EditVideoScreen({this.video});

  @override
  _EditVideoScreenState createState() => _EditVideoScreenState();
}

class _EditVideoScreenState extends State<EditVideoScreen> {
  final _formKey = GlobalKey<FormState>();
  late String _title;
  late String _description;
  late File? _videoFile;
  final _picker = ImagePicker();

  @override
  void initState() {
    super.initState();
    _title = widget.video?.title ?? '';
    _description = widget.video?.description ?? '';
    _videoFile = widget.video?.video ?? null;
  }

  Future<void> _pickVideo() async {
    try {
      FilePickerResult? result = await FilePicker.platform.pickFiles(
        type: FileType.video,
        allowCompression: true,
      );

      if (result != null) {
        if (result.files.single.path != null) {
          // Caso donde path está disponible
          setState(() {
            _videoFile = File(result.files.single.path!);
          });
        } else if (result.files.single.bytes != null) {
          // Caso donde bytes está disponible
          final tempDir = await getTemporaryDirectory();
          final tempFile = File('${tempDir.path}/${result.files.single.name}');
          await tempFile.writeAsBytes(result.files.single.bytes!);
          setState(() {
            _videoFile = tempFile;
          });
        } else {
          print('No video selected');
        }
      } else {
        print('No video selected');
      }
    } catch (e) {
      print('Error picking video: $e');
    }
  }

  void _saveForm() {
    if (_formKey.currentState!.validate()) {
      _formKey.currentState!.save();
      if (widget.video == null && _videoFile == null) {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(
          content: Text('Por favor selecciona un archivo de video'),
        ));
        return;
      }
      if (widget.video == null) {
        Provider.of<VideoProvider>(context, listen: false).addVideo(
          Video(id: 0, title: _title, video: _videoFile!, description: _description),
        );
      } else {
        Provider.of<VideoProvider>(context, listen: false).updateVideo(
          Video(id: widget.video!.id, title: _title, video: _videoFile ?? File(''), description: _description),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text(widget.video == null ? 'Crear Video' : 'Editar Video')),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Form(
          key: _formKey,
          child: Column(
            children: [
              TextFormField(
                initialValue: _title,
                decoration: InputDecoration(labelText: 'Título'),
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Por favor ingrese un título';
                  }
                  return null;
                },
                onSaved: (value) {
                  _title = value!;
                },
              ),
              TextFormField(
                initialValue: _description,
                decoration: InputDecoration(labelText: 'Descripción'),
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Por favor ingrese una descripción';
                  }
                  return null;
                },
                onSaved: (value) {
                  _description = value!;
                },
              ),
              if (widget.video == null)
                ElevatedButton(
                  onPressed: _pickVideo,
                  child: Text('Upload Video'),
                ),
              SizedBox(height: 20),
              ElevatedButton(
                onPressed: _saveForm,
                child: Text(widget.video == null ? 'Crear' : 'Guardar'),
              )
            ],
          ),
        ),
      ),
    );
  }
}