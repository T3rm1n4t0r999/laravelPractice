<header class="p-3 bg-dark text-white">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
            <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
                <svg class="bi me-2" width="40" height="32" role="img" aria-label="Bootstrap"><use xlink:href="#bootstrap"/></svg>
            </a>

            @auth
                {{auth()->user()->name}}
                <div class="text-end">
                    <a href="{{ route('logout.perform') }}" class="btn btn-outline-light me-2">Выйти из аккаунта</a>
                    <a href="{{ route('profile.profile') }}" class="btn btn-outline-light me-1">Личный кабинет</a>
                    <a href="{{ route('profile.import') }}" class="btn btn-outline-light me-1">Загрузить файл</a>
                </div>
            @endauth

            @guest
                <div class="text-end">
                    <a href="{{ route('login.perform') }}" class="btn btn-outline-light me-2">Войти в аккаунт</a>
                    <a href="{{ route('register.perform') }}" class="btn btn-warning">Зарегистрироваться</a>
                </div>
            @endguest
        </div>
    </div>
</header>
