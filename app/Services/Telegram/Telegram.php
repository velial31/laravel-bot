<?php

namespace App\Services\Telegram;

use Illuminate\Support\Facades\Cache;
use Telegram\Bot\Api;

class Telegram {

    const AWAITING_MESSAGE_KEY = 'await_message';

    public static function getApi()
    {
        return new Api(config('app.telegram.token'));
    }



    public static function getAwaitingMessageAction($userId)
    {
        return Cache::get(self::AWAITING_MESSAGE_KEY . ':' . $userId);
    }


    public static function setAwaitingMessageAction($userId, $action)
    {
        Cache::set(self::AWAITING_MESSAGE_KEY . ':' . $userId, $action);
    }



    public static function deleteAwaitingMessageAction($userId)
    {
        Cache::delete(self::AWAITING_MESSAGE_KEY . ':' . $userId);
    }

}
