<?php

namespace App\Notifications;

use App\Models\CheckSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

class CheckCycleCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public CheckSetting $setting
    ) {}

    public function via(object $notifiable): array
    {
        return [TelegramChannel::class];
    }

    public function toTelegram(object $notifiable): TelegramMessage
    {
        $domains = $this->setting->user->domains;
        $available = $domains->where('is_available', true)->count();
        $unavailable = $domains->where('is_available', false)->count();
        $total = $domains->count();

        return TelegramMessage::create()
            ->to($notifiable->telegram_chat_id)
            ->content(
                "*Check cycle completed*\n\n".
                "Setting: #{$this->setting->id}\n".
                "Total domains: {$total}\n".
                "Available: {$available}\n".
                "Unavailable: {$unavailable}"
            );
    }
}
