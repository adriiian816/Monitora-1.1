<?php

use App\Http\Controllers\Api\WhatsAppBotController;
use Illuminate\Support\Facades\Route;

Route::post('/whatsapp/webhook', [WhatsAppBotController::class, 'handle'])->name('api.whatsapp.webhook');
