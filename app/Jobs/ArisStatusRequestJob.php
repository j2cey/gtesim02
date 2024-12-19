<?php

namespace App\Jobs;

use App\Enums\QueueEnum;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use App\Models\Aris\ArisStatusRequest;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ArisStatusRequestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $arisstatusrequest_id;

    /**
     * Create a new job instance.
     */
    public function __construct(ArisStatusRequest $arisstatusrequest)
    {
        $this->onQueue(QueueEnum::ARISSTATUSREQUEST->value);

        $arisstatusrequest->setQueueing();
        $this->arisstatusrequest_id = $arisstatusrequest->id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $arisstatusrequest = ArisStatusRequest::getById($this->arisstatusrequest_id);

        if ($arisstatusrequest) {
            $arisstatusrequest->last_queueing_job_id = $this->job->getJobId();
            $arisstatusrequest->save();

            $arisstatusrequest->execNextEsim();
            $arisstatusrequest->endQueueing();
        } else {
            Log::error("ArisStatusRequestJob - ArisStatusRequest Model (" . $this->arisstatusrequest_id . ") NOT FOUND !");
        }
    }
}
