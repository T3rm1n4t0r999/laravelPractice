@extends('layouts.app-master')
@section('content')
    <div class="bg-light p-5 rounded">
        <form method='post' action="/file.php" enctype="multipart/form-data">
        <input type="hidden" name="MAX_FILE_SIZE" value="5000000">
        <input type='file' name='file[]' class='file-drop' id='file-drop' multiple required><br>
        <input class="btn btn-lg btn-primary" type='submit' value='Загрузить' >
        </form>
    </div>
@endsection

