<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

Route::get('/', function () {
    $nodeUrl = "http://localhost:4000/videos";

    try {
        $response = Http::get($nodeUrl);
        $videos = $response->json() ?? []; // garante que seja array
    } catch (\Exception $e) {
        $videos = [];
    }

    // Ajusta as URLs para passar pelo Laravel (proxy com Sanctum)
    $videos = array_map(fn($v) => [
        'name' => $v['name'],
        'url'  => url("/api/video/{$v['name']}"),
    ], $videos);

    return view('welcome', compact('videos'));
});
