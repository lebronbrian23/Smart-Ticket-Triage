<?php

use Illuminate\Support\Facades\Route;

/*Route::get('/', function () {
    return view('welcome');
});*/


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| All non-API requests should load the Vue SPA.
*/

Route::view('/{any}', 'welcome')->where('any', '.*');