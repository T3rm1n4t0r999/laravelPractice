@extends('layouts.app-master')
@section('content')
    <div class="bg-light p-5 rounded">
        <form id="js-form" method="POST" enctype="multipart/form-data" action="{{ route('import.upload') }}">
            @csrf
            <div class="mb-3">
                <input id="js-file" class="form-control" type="file" name="file" required>
            </div>
            <button type="submit" class="btn btn-primary">Загрузить файл</button>
        </form>

        <div id="result" class="mt-3">
            <!-- Сюда будет выводиться результат загрузки -->
        </div>

        <script src="/assets/jqueary/jquery.min.js"></script>
        <script src="/assets/jqueary/jquery.form.min.js"></script>

        <script>
            $('#js-form').on('submit', function(e) {
                e.preventDefault();

                // Очищаем предыдущие сообщения
                $('#result').html('');

                $(this).ajaxSubmit({
                    type: 'POST',
                    url: $(this).attr('action'),
                    dataType: 'json', // Указываем, что ожидаем JSON ответ
                    success: function(response) {
                        if(response.success) {
                            $('#result').html('<div class="alert alert-success">' + response.success + '</div>');
                        }
                    },
                    error: function(xhr) {
                        let errors = '';
                        if(xhr.status === 422) { // Ошибки валидации
                            const responseErrors = xhr.responseJSON.errors;
                            for(let field in responseErrors) {
                                errors += responseErrors[field].join('<br>') + '<br>';
                            }
                        } else if(xhr.responseJSON && xhr.responseJSON.error) {
                            errors = xhr.responseJSON.error;
                        } else {
                            errors = 'Произошла неизвестная ошибка';
                        }

                        $('#result').html('<div class="alert alert-danger">' + errors + '</div>');
                    }
                });
            });
        </script>
    </div>
@endsection
