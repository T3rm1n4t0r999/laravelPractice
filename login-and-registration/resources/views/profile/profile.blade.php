@extends('layouts.app-master')

@section('content')
    <div class="bg-light p-5 rounded">
        <p class="h3 mb-3 fw-normal">Файлы пользователя</p>
        <button id="get-files" class="btn btn-primary">Получить файлы</button>

        <div id="files-container" class="mt-3">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Имя файла</th>
                    <th>Токены</th>
                    <th>Скачивание</th>
                    <th>Пароль</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody id="files-table-body">
                <!-- Данные файлов будут вставлены сюда -->
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#get-files').on('click', function() {
                $.ajax({
                    url: '{{ route('files.getUserFiles') }}', // Убедитесь, что этот маршрут правильно настроен
                    type: 'GET',
                    success: function(files) {
                        let html = '';
                        if (files.length === 0) {
                            html = '<tr><td colspan="5">У вас нет загруженных файлов.</td></tr>';
                        } else {
                            files.forEach(function(file) {
                                html += '<tr>';
                                html += '<td>' + file.filename + '</td>';

                                // Выводим все токены на файл
                                html += '<td>';
                                if (file.tokens.length > 0) {
                                    file.tokens.forEach(function(token) {
                                        html += '<div><a href="{{ url('files') }}/' + token + '" target="_blank">' + token + '</a></div>';
                                    });
                                } else {
                                    html += 'Нет';
                                }
                                html += '</td>';

                                html += '<td>';
                                if (file.downloadables.length > 0) {
                                    file.downloadables.forEach(function(downloadable) {
                                        html += '<div>' + downloadable + '</div>'; // Отображаем токены вместо ссылок
                                    });
                                } else {
                                    html += 'Нет';
                                }
                                html += '</td>';

                                html += '<td>';
                                if (file.passwords.length > 0) {
                                    file.passwords.forEach(function(password) {
                                        html += '<div>' + password + '</div>'; // Отображаем токены вместо ссылок
                                    });
                                } else {
                                    html += 'Нет';
                                }
                                html += '</td>';

                                // Добавляем кнопку "Сгенерировать токен"
                                html += '<td><button class="btn btn-secondary generate-link" data-file-id="' + file.user_file_id + '">Сгенерировать токен</button></td>';
                                html += '</tr>';
                            });
                        }
                        $('#files-table-body').html(html);
                    },
                    error: function() {
                        $('#files-table-body').html('<tr><td colspan="5">Произошла ошибка при получении файлов.</td></tr>');
                    }
                });
            });

            // Обработка события клика на кнопке "Сгенерировать токен"
            $(document).on('click', '.generate-link', function() {
                const fileId = $(this).data('file-id');

                // AJAX-запрос для генерации токена на сервере
                $.ajax({
                    url: '{{ route('files.generateFileLink') }}', // Укажите правильный маршрут для генерации токена
                    type: 'POST',
                    data: {
                        id: fileId,
                        _token: '{{ csrf_token() }}' // Не забудьте добавить CSRF-токен
                    },
                    success: function(response) {
                        alert('Токен сгенерирован: ' + response.token); // Изменено на токен
                        // Можно также обновить таблицу, чтобы отобразить новый токен
                        $('#get-files').click(); // Перезагрузить файлы
                    },
                    error: function() {
                        alert('Произошла ошибка при генерации токена.');
                    }
                });
            });
        });
    </script>
@endsection
