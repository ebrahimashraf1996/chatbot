<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Webhook\WhatsappWebhookController;

Route::post('/webhooks/whatsapp', [WhatsappWebhookController::class, 'handle']);

