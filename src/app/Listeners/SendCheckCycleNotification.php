<?php

namespace App\Listeners;

use App\Events\CheckCycleCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\CheckCycleCompletedNotification;

class SendCheckCycleNotification implements ShouldQueue
{
    public function handle(CheckCycleCompleted $event): void
    {
        $user = $event->setting->user;

        if (! $user->telegram_chat_id) {
            return;
        }

        $user->notify(new CheckCycleCompletedNotification($event->setting));
    }
}
