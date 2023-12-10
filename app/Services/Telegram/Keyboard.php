<?php

namespace App\Services\Telegram;

class Keyboard {


    public static function start()
    {

        $keyboard = [
            [
                [
                    'text' => 'test',
                    'callback_data' => json_encode([
                        'action' => 'test',
                    ]),
                ]
            ]
        ];

        return json_encode([
            'inline_keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);

    }
}
