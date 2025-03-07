@extends('layouts.app-master')
@section('content')
    <div class="bg-light p-5 rounded">
        <form id="js-form" method="post">
            <input id="js-file" type="file" name="file">
        </form>

        <div id="result">
            <!-- Сюда выводится результат из upload_ajax.php -->
        </div>

        <script src="/assets/jqueary/jquery.min.js"></script>
        <script src="/assets/jqueary/jquery.form.min.js"></script>

        <script>
            $('#js-file').change(function() {
                $('#js-form').ajaxSubmit({
                    type: 'POST',
                    url: '/upload.php',
                    target: '#result',
                    success: function() {
                        // После загрузки файла очистим форму.
                        $('#js-form')[0].reset();
                    }
                });
            });
        </script>
    </div>
@endsection
