@extends('layouts.auth-master')
@section('content')
    <div class="d-flex justify-content-center">
        <form method="post" action="{{ route('login.perform') }}" class="col-md-6 col-lg-4">

            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <div class="text-center">
                <img class="mb-4 mt-5" src="{!! url('assets/images/login.png') !!}" alt="" width="128" height="128">
            </div>

            <h1 class="h3 mb-3 fw-normal text-center">Авторизация</h1>

            @include('layouts.partials.messages')

            <div class="form-group form-floating mb-3">
                <input type="text" class="form-control" name="username" value="{{ old('username') }}" placeholder="Username" required="required" autofocus>
                <label for="floatingName">Почта или логин</label>
                @if ($errors->has('username'))
                    <span class="text-danger text-left">{{ $errors->first('username') }}</span>
                @endif
            </div>

            <div class="form-group form-floating mb-3">
                <input type="password" class="form-control" name="password" value="{{ old('password') }}" placeholder="Password" required="required">
                <label for="floatingPassword">Пароль</label>
                @if ($errors->has('password'))
                    <span class="text-danger text-left">{{ $errors->first('password') }}</span>
                @endif
            </div>

            <button class="w-100 btn btn-lg btn-primary" type="submit">Войти в аккаунт</button>

            <div class="mt-3 text-center">
                <a href="{{ route('register.show') }}" class="btn btn-link">Зарегистрироваться</a>
            </div>

            @include('auth.partials.copy')
        </form>
    </div>
@endsection
