<?php

namespace App\Listeners;

use App\Models\User;
use App\Events\UserActivatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserActivatedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserActivatedEvent $event): void
    {
        $user = User::getById( $event->user_id );
        $user->sendMailAccountInfos( $event->pwd );
    }
}
