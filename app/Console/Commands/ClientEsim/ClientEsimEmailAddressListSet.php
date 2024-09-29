<?php

namespace App\Console\Commands\ClientEsim;

use Illuminate\Console\Command;
use App\Models\Esims\ClientEsim;

class ClientEsimEmailAddressListSet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clientesim:emailaddresslist-set';

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
        $clientesims = ClientEsim::all();

        foreach ($clientesims as $clientesim) {
            $clientesim->setEmailAddressList();
        }
    }
}
