@if(isset($error))
    <div class="alert alert-danger">
        <strong>Помилка:</strong> {{ $error }}
    </div>
@else
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">{{ $movie['Title'] }} ({{ $movie['Year'] }})</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 text-center">
                    <img src="{{ $movie['Poster'] }}" alt="Постер" class="img-fluid mb-3" style="max-height: 300px;">
                </div>
                <div class="col-md-8">
                    <p><strong>Жанр:</strong> {{ $movie['Genre'] }}</p>
                    <p><strong>Режисер:</strong> {{ $movie['Director'] }}</p>
                    <p><strong>Актори:</strong> {{ $movie['Actors'] }}</p>
                    <p><strong>Сюжет:</strong> {{ $movie['Plot'] }}</p>
                    <p><strong>Рейтинг IMDB:</strong> {{ $movie['imdbRating'] }}/10</p>
                    <p><strong>Тривалість:</strong> {{ $movie['Runtime'] }}</p>
                    <p><strong>Країна:</strong> {{ $movie['Country'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <h4>Надіслати результати на email</h4>
        <form id="emailForm">
            <input type="hidden" name="movie_data" value="{{ json_encode($movie) }}">
            <div class="mb-3">
                <label for="email" class="form-label">Адреса отримувача</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="subject" class="form-label">Заголовок листа</label>
                <input type="text" class="form-control" id="subject" name="subject" value="Інформація про фільм: {{ $movie['Title'] }}" required>
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Текст листа</label>
                <textarea class="form-control" id="message" name="message" rows="3" required>Добрий день! Ось інформація про фільм "{{ $movie['Title'] }}".</textarea>
            </div>
            <button type="submit" class="btn btn-success">Надіслати</button>
        </form>
    </div>

    <script>
        $('#emailForm').submit(function(e) {
            e.preventDefault();
            let formData = $(this).serialize();

            $.post("{{ route('movie.sendEmail') }}", formData, function(response) {
                alert(response.success);
            }).fail(function() {
                alert('Помилка надсилання листа');
            });
        });
    </script>
@endif
