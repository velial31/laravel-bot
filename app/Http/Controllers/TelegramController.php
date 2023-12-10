<?php

namespace App\Http\Controllers;

use App\Services\Telegram\Handlers\RequestHandler;
use App\Services\Telegram\Telegram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    public function webhook(Request $request)
    {
        $telegram = Telegram::getApi();
        $update = $telegram->getWebhookUpdate();

        try {
            $handler = new RequestHandler($update);
            $handler->handle();
        }
        catch (\Exception $exception) {
            Log::info('Telegram controller error: ' . 'Error: ' . $exception->getMessage() . '. File: ' . $exception->getFile() . '. Line: ' . $exception->getLine());
        }

        return response(null, 200);
    }
}
