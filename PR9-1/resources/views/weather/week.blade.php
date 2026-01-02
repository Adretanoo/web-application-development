@if(isset($error))
    <div class="alert alert-danger">
        <strong>Помилка:</strong> {{ $error }}
    </div>
@else
    <h2 class="text-center mb-4">Прогноз на 7 днів у {{ $city }}, {{ $country }}</h2>

    <div class="row">
        @foreach($forecast as $day)
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="card shadow h-100 text-center">
                    <div class="card-header">
                        <strong>{{ \Carbon\Carbon::parse($day['date'])->format('d.m (l)') }}</strong>
                    </div>
                    <div class="card-body">
                        <img src="https:{{ $day['day']['condition']['icon'] }}" alt="Погода" class="mb-2">
                        <p class="fw-bold">{{ $day['day']['maxtemp_c'] }}° / {{ $day['day']['mintemp_c'] }}°</p>
                        <small>{{ $day['day']['condition']['text'] }}</small><br>
                        <small>Опади: {{ $day['day']['daily_chance_of_rain'] }}%</small>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

<div class="text-center mt-4">
    <button onclick="history.back()" class="btn btn-outline-primary">← Назад</button>
</div>
