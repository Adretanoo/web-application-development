<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\MovieInfoMail;

class MovieController extends Controller
{
    private $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.omdb.key');
    }

    public function index()
    {
        return view('movie.index');
    }

    public function search(Request $request)
    {
        $title = $request->input('title');

        if (!$title) {
            return view('movie.result', ['error' => 'Введіть назву фільму']);
        }

        $response = Http::get('http://www.omdbapi.com/', [
            'apikey' => $this->apiKey,
            't' => $title,
            'plot' => 'full',
        ]);

        if ($response->failed() || $response['Response'] === 'False') {
            $error = $response['Error'] ?? 'Помилка отримання даних';
            return view('movie.result', compact('error'));
        }

        $movie = $response->json();

        return view('movie.result', compact('movie'));
    }

    public function sendEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'movie_data' => 'required|json',
        ]);

        $movie = json_decode($request->movie_data, true);

        Mail::to($request->email)->send(new MovieInfoMail($movie, $request->message, $request->subject));

        return response()->json(['success' => 'Лист надіслано!']);
    }
}
