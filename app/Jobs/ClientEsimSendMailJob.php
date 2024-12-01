<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Models\Person\PhoneNum;
use Illuminate\Queue\SerializesModels;
use App\Events\ClientEsimCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ClientEsimSendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $phonenum_id;

    /**
     * Create a new job instance.
     */
    public function __construct(PhoneNum $phonenum)
    {
        $this->phonenum_id = $phonenum->id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        event( new ClientEsimCreatedEvent( PhoneNum::getById($this->phonenum_id) ));
    }
}
