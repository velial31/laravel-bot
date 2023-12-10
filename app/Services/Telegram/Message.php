<?php


namespace App\Services\Telegram;


use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\FileUpload\InputFile;

class Message
{
    private array $data = [
        'parse_mode' => 'html',
        'disable_web_page_preview' => false
    ];

    private Api $telegram;


    public function __construct()
    {
        $this->telegram = Telegram::getApi();
    }


    public static function getInstance()
    {
        return new static();
    }


    public function text($text)
    {
        $this->data['text'] = $text;

        return $this;
    }



    public function keyboard($keyboard)
    {
        $this->data['reply_markup'] = $keyboard;

        return $this;
    }


    public function image($imageUrl)
    {
        $this->data['photo'] = $imageUrl;

        return $this;
    }



    public function disableWebPagePreview()
    {
        $this->data['disable_web_page_preview'] = true;

        return $this;
    }


    private function setChatId($chatId)
    {
        $this->data['chat_id'] = $chatId;

        return $this;
    }



    private function setMessageId($messageId)
    {
        $this->data['message_id'] = $messageId;

        return $this;
    }




    public function send($chatId)
    {
        $this->setChatId($chatId);

        try {
            if (isset($this->data['photo'])) {
                $this->data['caption'] = $this->data['text'];
                $this->data['photo'] = new InputFile($this->data['photo']);
                $message = $this->telegram->sendPhoto($this->data);
            }
            else $message = $this->telegram->sendMessage($this->data);
        }
        catch (\Exception $exception) {
            Log::error('Error message sending: ' . $exception->getMessage());
            return false;
        }

        return $message;
    }



    public function edit($chatId, $messageId)
    {
        $this->setChatId($chatId);
        $this->setMessageId($messageId);

        try {
            $message = $this->telegram->editMessageText($this->data);
        }
        catch (\Exception $exception) {
            Log::error('Error message edit: ' . $exception->getMessage());
            return false;
        }

        return $message;
    }



    // Если не получается отредактировать - удаляет старое сообщение и отправляет новое
    public function forceEdit($chatId, $messageId)
    {
        $message = $this->edit($chatId, $messageId);
        if ($message) return $message;

        Message::getInstance()
            ->delete($chatId, $messageId);

        return $this->send($chatId);
    }



    public function delete($chatId, $messageId)
    {
        $this->setChatId($chatId);
        $this->setMessageId($messageId);

        try {
            $message = $this->telegram->deleteMessage($this->data);
        }
        catch (\Exception $exception) {
            Log::error('Error message delete: ' . $exception->getMessage());
            return false;
        }
    }

}
