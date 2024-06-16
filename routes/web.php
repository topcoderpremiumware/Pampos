<?php

use App\Http\Controllers\TelegramWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::any('telegram_webhook', [TelegramWebhookController::class,'webhook']);
Route::get('telegram_setwebhook', [TelegramWebhookController::class,'setWebhook']);


