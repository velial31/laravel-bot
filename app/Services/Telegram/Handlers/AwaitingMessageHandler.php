<?php

namespace App\Services\Telegram\Handlers;

use App\Services\Telegram\Message;
use app\Services\Telegram\Telegram;

class AwaitingMessageHandler extends MessageHandler
{

    private string $action = '';


    public function setAction($action)
    {
        $this->action = $action;
    }


    public function handle()
    {
        switch ($this->action) {
            case 'await_message':
                $this->test();
                break;
        }
    }


    private function test()
    {
        Message::getInstance()
            ->text('answer received')
            ->send($this->user['chat_id']);

        Telegram::deleteAwaitingMessageAction($this->user['id']);
    }

}
