<?php

namespace App\Console\Commands\Esim;

use Carbon\Carbon;
use App\Models\Esims\Esim;
use Illuminate\Console\Command;
use App\Jobs\EsimLongAttributionJob;

class EsimLongAttributionLaunch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'esim:long-attribution-launch';

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
        $long_attribution_esims = Esim::inAttributionGet();
        if ( $long_attribution_esims ) {
            foreach ( $long_attribution_esims as $long_attribution_esim ) {
                EsimLongAttributionJob::dispatch($long_attribution_esim);
            }
        }
    }
}
