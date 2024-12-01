<?php

namespace App\Listeners;

use App\Models\Person\PhoneNum;
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
        $phonenum = PhoneNum::getById( $event->phonenum_id );
        $client = $phonenum->hasphonenum;

        $client->sendmailprofile($phonenum);
    }
}
