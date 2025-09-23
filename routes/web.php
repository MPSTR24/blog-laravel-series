<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


// Post Two Content
Route::get('/baseline', static function () {
    $users = User::all();

    return view('baseline', [
        'users' => $users->map(fn ($user) => [
            'id' => $user->id,
            'name' => $user->name,
            'posts_count' => $user->posts->count(),
        ]),
    ]);

//    Old original query that returns raw data
//    return $users->map(fn ($user) => [
//        'id' => $user->id,
//        'name' => $user->name,
//        'posts_count' => $user->posts->count(),
//    ]);
});



