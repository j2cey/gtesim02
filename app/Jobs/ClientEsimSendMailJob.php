<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Models\Employes\PhoneNum;
use Illuminate\Queue\SerializesModels;
use App\Events\ClientEsimCreatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ClientEsimSendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $phonenum;

    /**
     * Create a new job instance.
     */
    public function __construct(PhoneNum $phonenum)
    {
        $this->phonenum = $phonenum;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        event( new ClientEsimCreatedEvent( $this->phonenum ));
    }
}
