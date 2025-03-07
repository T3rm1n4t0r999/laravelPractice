@extends('layouts.app-master')

@section('content')
    <div class="bg-light p-5 rounded">
        @auth
            <p class="lead">Вы авторизованы.</p>
        @endauth

        @guest
            <p class="lead">Войдите в аккаунт, чтобы получить доступ к данным</p>
        @endguest
    </div>
@endsection
