@extends('layouts.app-master')

@section('content')
    <div class="bg-light p-5 rounded text-center">
        <h1 class="display-4">Ошибка</h1>

        @auth
            <p class="lead">Что-то пошло не так.</p>
            <p>Пожалуйста, попробуйте позже или обратитесь к администратору.</p>
        @endauth

        @guest
            <p class="lead">Для просмотра этой страницы необходимо авторизоваться</p>
            <a href="{{ route('login.show') }}" class="btn btn-primary">Войти</a>
        @endguest

        <hr class="my-4">
        <a href="{{ url('/') }}" class="btn btn-secondary">Вернуться на главную</a>
    </div>
@endsection
