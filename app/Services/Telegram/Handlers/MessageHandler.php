<?php

namespace App\Services\Telegram\Handlers;

use App\Models\User;
use App\Services\Telegram\Message;
use App\Services\Telegram\Telegram;

class MessageHandler
{

    protected string $message = '';
    protected User $user;

    public function __construct($user, $message)
    {
        $this->user = $user;
        $this->message = $message;
    }

    public function handle()
    {
        switch ($this->message) {
            case 'test':
                $this->test();
                break;
        }
    }



    private function test()
    {
        Message::getInstance()
            ->text('test message')
            ->send($this->user['chat_id']);


        Telegram::setAwaitingMessageAction($this->user['id'], 'await_answer');
    }

}
