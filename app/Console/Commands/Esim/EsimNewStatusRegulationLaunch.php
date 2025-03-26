<?php

namespace App\Console\Commands\Esim;

use App\Models\Esims\Esim;
use Illuminate\Console\Command;
use App\Jobs\EsimNewStatusRegulationJob;

class EsimNewStatusRegulationLaunch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'esim:newstatus-regulation-launch';

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
        $new_esims = Esim::isNewGet();
        if ( $new_esims ) {
            foreach ( $new_esims as $new_esim ) {
                EsimNewStatusRegulationJob::dispatch($new_esim);
            }
        }
    }
}
