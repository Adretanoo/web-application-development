<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    private $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.weatherapi.key');
    }

    public function index()
    {
        return view('weather.index');
    }

    public function today(Request $request)
    {
        $city = $request->input('city', 'Kyiv');

        $response = Http::get('http://api.weatherapi.com/v1/current.json', [
            'key' => $this->apiKey,
            'q'   => $city,
            'aqi' => 'no'
        ]);

        if ($response->failed() || isset($response->json()['error'])) {
            $error = $response->json()['error']['message'] ?? 'Помилка отримання даних';
            return response()->view('weather.today', compact('error', 'city'), 200);
        }

        $data = $response->json();

        return view('weather.today', [
            'city'    => $data['location']['name'],
            'country' => $data['location']['country'],
            'weather' => $data['current'],
            'error'   => null
        ]);
    }

    public function week(Request $request)
    {
        $city = $request->input('city', 'Kyiv');

        $response = Http::get('http://api.weatherapi.com/v1/forecast.json', [
            'key' => $this->apiKey,
            'q'   => $city,
            'days' => 7,
            'aqi' => 'no',
            'alerts' => 'no'
        ]);

        if ($response->failed() || isset($response->json()['error'])) {
            $error = $response->json()['error']['message'] ?? 'Помилка отримання даних';
            return view('weather.week', compact('error', 'city'));
        }

        $data = $response->json();

        return view('weather.week', [
            'city'     => $data['location']['name'],
            'country'  => $data['location']['country'],
            'forecast' => $data['forecast']['forecastday'],
            'error'    => null
        ]);
    }
}
