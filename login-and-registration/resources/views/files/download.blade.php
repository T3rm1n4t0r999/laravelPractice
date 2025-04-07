@extends('layouts.app-master')

@section('content')
    <div class="container">
        <h2>Скачать файл: {{ $filename }}</h2> <!-- Изменено на $file_link -->

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('files.download', $token_id) }}"> <!-- Изменено на $file_link -->
            @csrf
            <div class="mb-3">
                <label for="password" class="form-label">Введите пароль для скачивания файла</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Скачать файл</button>
        </form>
    </div>
@endsection
