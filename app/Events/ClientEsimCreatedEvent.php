<?php

namespace App\Events;

use App\Models\Person\PhoneNum;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ClientEsimCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $phonenum_id;

    /**
     * Create a new event instance.
     */
    public function __construct(PhoneNum $phonenum)
    {
        //\Log::info("clientesim received ClientEsimCreatedEvent : " . json_encode( $phonenum ) );
        $this->phonenum_id = $phonenum->id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
