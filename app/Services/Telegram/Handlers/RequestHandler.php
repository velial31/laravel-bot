<?php


namespace App\Services\Telegram\Handlers;

use App\Models\User;
use App\Services\Telegram\Telegram;
use Illuminate\Support\Facades\App;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;

class RequestHandler
{

    private string $requestType = '';

    protected Api $telegram;
    protected Update $update;
    protected int $chatId;
    protected User $user;
    protected string $lang = 'ru';

    public function __construct($update)
    {
        $this->telegram = Telegram::getApi();

        $this->setUpdate($update);
        $this->setRequestType();
        $this->setChatId();
        $this->setUser();
        $this->setLang();
    }


    private function setUpdate($update)
    {
        $this->update = $update;
    }


    private function setUser()
    {
        if ($this->requestType == 'message' || $this->requestType == 'command') {
            $username = $this->update['message']['from']['username'] ?? null;
        }
        elseif($this->requestType == 'callback') {
            $username = $this->update['callback_query']['from']['username'] ?? null;
        }

        $this->user = User::query()->firstOrCreate(
            [
                'chat_id' => $this->chatId
            ],
            [
                'chat_id' => $this->chatId,
                'username' => $username,

            ]
        );

    }


    private function setLang()
    {
        if ($this->requestType == 'message' || $this->requestType == 'command') {
            $this->lang = $this->update['message']['from']['language_code'];
        }
        elseif($this->requestType == 'callback') {
            $this->lang = $this->update['callback_query']['from']['language_code'];
        }

        $this->lang = $this->lang == 'ru' ? 'ru' : 'en';

        if (!$this->user['lang']) $this->user->update(['lang' => $this->lang]);

        App::setLocale($this->lang);
    }


    private function setRequestType()
    {
        if (isset($this->update['message']['message_id'])) {
            $text = $this->update['message']['text'] ?? '';

            if ($text) {
                if ($text[0] == '/') $this->requestType = 'command';
                else $this->requestType = 'message';
            }
        }
        elseif(isset($this->update['callback_query'])) $this->requestType = 'callback';
    }



    private function setChatId()
    {
        if ($this->requestType == 'message' || $this->requestType == 'command') {
            $this->chatId = $this->update['message']['chat']['id'];
        }
        elseif($this->requestType == 'callback') {
            $this->chatId = $this->update['callback_query']['message']['chat']['id'];
        }
        else die();
    }


    public function handle()
    {
        $handler = false;

        if ($this->requestType == 'command') $handler = new CommandHandler($this->user, $this->update['message']['text']);
        elseif ($this->requestType == 'message') {
            $enterTextAction = Telegram::getAwaitingMessageAction($this->user['id']);

            if ($enterTextAction) {
                $handler = new AwaitingMessageHandler($this->user, $this->update['message']['text']);
                $handler->setAction($enterTextAction);
            }
            else {
                $handler = new MessageHandler($this->user, $this->update['message']['text']);
            }
        }
        elseif ($this->requestType == 'callback') {
            $data = $this->update['callback_query']['data'];
            $messageId = $this->update['callback_query']['message']['message_id'];

            $handler = new CallbackHandler($this->user, $data, $messageId);
        }

        if ($handler) $handler->handle();
    }
}
