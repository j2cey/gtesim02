<?php

namespace App\Console\Commands\Esim;

use Illuminate\Console\Command;
use App\Jobs\ArisStatusRequestJob;
use App\Models\Aris\ArisStatusRequest;

class EsimArisStatusRequestNext extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'esim:arisstatus-requestnext';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ( ArisStatusRequest::isRequestsActivated() ) {
            $curr_request = ArisStatusRequest::whereRequestStatus(ArisStatusRequest::$STATUS_CODE_WAITING)->orderBy('id', 'asc')->first();
            if ($curr_request) {
                //$curr_request->execNextEsim();
                ArisStatusRequestJob::dispatch($curr_request);
            } else {
                ArisStatusRequest::startNew();
            }
        }
    }
}
