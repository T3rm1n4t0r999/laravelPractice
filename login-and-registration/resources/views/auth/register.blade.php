@extends('layouts.auth-master')
@section('content')
    <div class="d-flex justify-content-center">
        <form method="post" action="{{ route('register.perform') }}" class="col-md-6 col-lg-4">

            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <div class="text-center">
                <img class="mb-4 mt-3" src="{!! url('assets/images/register.png') !!}" alt="" width="128" height="128">
            </div>

            <h1 class="h3 mb-3 fw-normal text-center">Регистрация</h1>

            <div class="form-group form-floating mb-3">
                <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="name@example.com" required="required" autofocus>
                <label for="floatingEmail">Email</label>
                @if ($errors->has('email'))
                    <span class="text-danger text-left">{{ $errors->first('email') }}</span>
                @endif
            </div>

            <div class="form-group form-floating mb-3">
                <input type="text" class="form-control" name="username" value="{{ old('username') }}" placeholder="Username" required="required">
                <label for="floatingName">Имя пользователя</label>
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

            <div class="form-group form-floating mb-3">
                <input type="password" class="form-control" name="password_confirmation" value="{{ old('password_confirmation') }}" placeholder="Confirm Password" required="required">
                <label for="floatingConfirmPassword">Подтверждение пароля</label>
                @if ($errors->has('password_confirmation'))
                    <span class="text-danger text-left">{{ $errors->first('password_confirmation') }}</span>
                @endif
            </div>

            <button class="w-100 btn btn-lg btn-primary" type="submit">Зарегистрироваться</button>

            <div class="mt-3 text-center">
                <a href="{{ route('login.show') }}" class="btn btn-link">Уже есть аккаунт</a>
            </div>

            @include('auth.partials.copy')
        </form>
    </div>
@endsection
