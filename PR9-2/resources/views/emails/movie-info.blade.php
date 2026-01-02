<h1>Інформація про фільм: {{ $movie['Title'] }}</h1>

<p>{{ $customMessage }}</p>

<ul>
    <li><strong>Рік:</strong> {{ $movie['Year'] }}</li>
    <li><strong>Жанр:</strong> {{ $movie['Genre'] }}</li>
    <li><strong>Режисер:</strong> {{ $movie['Director'] }}</li>
    <li><strong>Актори:</strong> {{ $movie['Actors'] }}</li>
    <li><strong>Сюжет:</strong> {{ $movie['Plot'] }}</li>
    <li><strong>Рейтинг IMDB:</strong> {{ $movie['imdbRating'] }}/10</li>
</ul>

<p>Постер: <img src="{{ $movie['Poster'] }}" alt="Постер" style="max-width: 200px;"></p>

<p>З повагою,<br>PR9-2 App</p>
