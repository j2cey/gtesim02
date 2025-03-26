<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Enums\QueueEnum;
use App\Models\Esims\Esim;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EsimNewStatusRegulationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public int $esim_id;

    /**
     * Create a new job instance.
     */
    public function __construct(Esim $esim)
    {
        $this->onQueue(QueueEnum::ESIMSTATUSNEWREGULATION->value);
        $this->esim_id = $esim->id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $esim = Esim::getById($this->esim_id);
        if ($esim) {
            $esim->load('phonenum');
            Log::info("esim_id: " . $esim->id . ", phonenum ? " . ($esim->phonenum ? $esim->phonenum->phone_number : 'NO'));
            if ($esim->phonenum) {
                $esim->setStatutAttribue(false);
            }
        }
    }
}
