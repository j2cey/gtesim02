<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Enums\QueueEnum;
use App\Models\Esims\Esim;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;

class EsimLongAttributionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public static int $interval_days = 1;
    public int $esim_id;

    /**
     * Create a new job instance.
     */
    public function __construct(Esim $esim)
    {
        $this->onQueue(QueueEnum::ESIMLONGATTRIBUTION->value);
        $this->esim_id = $esim->id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $esim = Esim::getById($this->esim_id);
        if ($esim && $esim->is_in_attribution) {
            $diffInDays = $esim->updated_at->diffInDays(Carbon::now());

            if( $diffInDays >= self::$interval_days ) {
                $esim->setStatutFree();
            }
        }
    }
}
