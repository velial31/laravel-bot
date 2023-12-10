<?php

namespace App\Services\Telegram\Handlers;

use App\Models\User;
use App\Services\Telegram\Keyboard;
use App\Services\Telegram\Message;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;


class CallbackHandler
{

    private array $data = [];
    private int $messageId;

    protected User $user;


    public function __construct($user, $data, $messageId)
    {
        $this->user = $user;
        $this->data = json_decode($data, true);
        $this->messageId = $messageId;
    }


    public function handle()
    {
        $action = $this->data['action'] ?? false;
        if (!$action) return;

        switch ($action) {
            case 'test':
                $this->test();
                break;
        }
    }


    private function test()
    {
        $message = Message::getInstance()
            ->text('test callback')
            ->edit($this->user['chat_id'], $this->messageId);

        if (!$message) return;
    }

}
