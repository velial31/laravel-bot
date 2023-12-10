<?php

namespace App\Services\Telegram\Handlers;

use App\Services\Telegram\Keyboard;
use App\Services\Telegram\Message;

class CommandHandler extends MessageHandler
{
    private string $payload = '';

    public function handle()
    {
        $this->prepareCommand();

        switch ($this->message) {
            case '/start':
                $this->sendStartMessage();
                break;
        }
    }


    private function prepareCommand()
    {
        $message = explode(' ', $this->message);

        $this->message = $message[0];
        $this->payload = $message[1] ?? '';
    }



    private function sendStartMessage()
    {
        $keyboard = Keyboard::start();

        Message::getInstance()
            ->text('start')
            ->keyboard($keyboard)
            ->send($this->user['chat_id']);
    }
}
