@extends('layouts.app')

@section('content')
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold">PR9-2: Пошук інформації про фільми</h1>
    </div>

    <div class="card mx-auto shadow" style="max-width: 600px;">
        <div class="card-body">
            <form id="movieForm" class="mb-4">
                <div class="input-group">
                    <input type="text" class="form-control form-control-lg" name="title" id="title"
                           placeholder="Введіть назву фільму (наприклад: Inception)" required>
                    <button class="btn btn-primary btn-lg" type="submit">Пошук</button>
                </div>
            </form>
        </div>
    </div>

    <div id="result" class="mt-5"></div>

    <script>
        $('#movieForm').submit(function(e) {
            e.preventDefault();
            let title = $('#title').val().trim();
            if (!title) return;

            $('#result').html('<div class="text-center"><div class="spinner-border"></div></div>');

            $.get("{{ route('movie.search') }}", { title: title }, function(data) {
                $('#result').html(data);
            }).fail(function() {
                $('#result').html('<div class="alert alert-danger">Помилка сервера</div>');
            });
        });
    </script>
@endsection
