@extends('layouts.app')

@section('content')
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold">Погода туй</h1>
    </div>

    <div class="card mx-auto shadow" style="max-width: 600px;">
        <div class="card-body">
            <form id="weatherForm" class="mb-4">
                <div class="input-group">
                    <input type="text" class="form-control form-control-lg" name="city" id="city"
                           placeholder="Введіть місто (наприклад: Kyiv, Lviv)" required>
                    <button class="btn btn-primary btn-lg" type="submit">Погода сьогодні</button>
                </div>
            </form>

            <div class="d-grid gap-2">
                <button onclick="loadWeek()" class="btn btn-success btn-lg">Прогноз на 7 днів</button>
                <a href="/" class="btn btn-outline-secondary">Оновити форму</a>
            </div>
        </div>
    </div>

    <div id="result" class="mt-5"></div>

    <script>
        $('#weatherForm').submit(function(e) {
            e.preventDefault();
            let city = $('#city').val().trim();
            if (!city) return;

            $('#result').html('<div class="text-center"><div class="spinner-border"></div></div>');

            $.get("{{ route('weather.today') }}", { city: city }, function(data) {
                $('#result').html(data);
            }).fail(function() {
                $('#result').html('<div class="alert alert-danger">Помилка сервера</div>');
            });
        });

        function loadWeek() {
            let city = $('#city').val().trim() || 'Kyiv';
            $('#result').html('<div class="text-center"><div class="spinner-border"></div></div>');

            $.get("{{ route('weather.week') }}", { city: city }, function(data) {
                $('#result').html(data);
            });
        }
    </script>
@endsection
