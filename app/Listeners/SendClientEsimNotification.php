<?php

namespace App\Listeners;

use App\Events\ClientEsimCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendClientEsimNotification
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
    public function handle(ClientEsimCreatedEvent $event): void
    {
        //\Log::info("event received SendClientEsimNotification : " . json_encode( $event ) );
        $client = $event->phonenum->hasphonenum;
        $client->sendmailprofile($event->phonenum);
    }
}
