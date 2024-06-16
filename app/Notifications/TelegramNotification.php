<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

class TelegramNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $message;
    public $buttonLinks;
    public $buttonCallbacks;
    public $options;

    public function __construct($data)
    {
        $this->message = $data['message'] ?? null;
        $this->buttonLinks = $data['buttonLinks'] ?? null;
        $this->buttonCallbacks = $data['buttonCallbacks'] ?? null;
        $this->options = $data['options'] ?? null;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via()
    {
        return ["telegram"];
    }


    public function toTelegram($notifiable)
    {
        $tgMessage = TelegramMessage::create();
        if(!empty($notifiable->telegram_user_i)){
            $tgMessage->to($notifiable->telegram_user_i);
        }
        $tgMessage->content($this->message);
        if(!empty($this->options)){
            $tgMessage->options($this->options);
        }
        if (!empty($this->buttonLinks)) {
            foreach ($this->buttonLinks as $index => $buttonLink) {
                $tgMessage->button($buttonLink['label'], url($buttonLink['url']));
            }
        }
        if (!empty($this->buttonCallbacks)) {
            foreach ($this->buttonCallbacks as $index => $buttonCallback) {
                $tgMessage->buttonWithCallback($buttonCallback['label'], $buttonCallback['data'],1);
            }
        }
        return $tgMessage;
    }
}
