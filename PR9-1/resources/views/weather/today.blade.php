@if(isset($error))
    <div class="alert alert-danger">
        <strong>Помилка:</strong> {{ $error }}
    </div>
@else
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Погода зараз у {{ $city }}, {{ $country }}</h3>
        </div>
        <div class="card-body text-center">
            <img src="https:{{ $weather['condition']['icon'] }}" alt="Іконка погоди" class="mb-3" style="width: 100px;">
            <h2 class="display-4">{{ $weather['temp_c'] }}°C</h2>
            <p class="lead">{{ $weather['condition']['text'] }}</p>
            <div class="row mt-4">
                <div class="col">
                    <strong>Вологість:</strong> {{ $weather['humidity'] }}%
                </div>
                <div class="col">
                    <strong>Вітер:</strong> {{ $weather['wind_kph'] }} км/год ({{ $weather['wind_dir'] }})
                </div>
                <div class="col">
                    <strong>Тиск:</strong> {{ $weather['pressure_mb'] }} мбар
                </div>
            </div>
        </div>
    </div>
@endif

<div class="text-center mt-4">
    <button onclick="history.back()" class="btn btn-outline-primary">← Назад</button>
</div>
