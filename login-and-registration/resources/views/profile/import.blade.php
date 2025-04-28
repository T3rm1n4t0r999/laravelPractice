@extends('layouts.app-master')
@section('content')
    <div class="bg-light p-5 rounded">
        <form id="js-form" method="POST" enctype="multipart/form-data" action="{{ route('import.upload') }}">
            @csrf <!-- Не забудьте добавить CSRF-токен для защиты -->
            <input id="js-file" type="file" name="file" required>
            <button type="submit">Загрузить файл</button>
        </form>

        <div id="result">
            <!-- Сюда будет выводиться результат загрузки -->
        </div>

        <script src="/assets/jqueary/jquery.min.js"></script>
        <script src="/assets/jqueary/jquery.form.min.js"></script>

        <script>
            $('#js-form').on('submit', function(e) {
                e.preventDefault(); // Отменяем стандартное поведение формы


                $(this).ajaxSubmit({
                    type: 'POST',
                    url: $(this).attr('action'),
                    success: function(response) {
                        // Обработка успешного ответа
                        $('#result').html('<span class="success" style="color: green">' + response.success + '</span>');
                    },
                    error: function(xhr) {
                        // Обработка ошибок
                        var errors = xhr.responseJSON.error;
                        var errorMessages = '';
                        for (var key in errors) {
                            errorMessages += errors[key].join('<br>');
                        }
                        $('#result').html('<span class="error" style="color: red">' + errorMessages + '</span>');
                    }
                });
            });
        </script>


    </div>
@endsection
