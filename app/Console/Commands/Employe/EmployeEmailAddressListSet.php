<?php

namespace App\Console\Commands\Employe;

use Illuminate\Console\Command;
use App\Models\Employes\Employe;

class EmployeEmailAddressListSet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employe:emailaddresslist-set';

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
        $employes = Employe::all();

        foreach ($employes as $employe) {
            $employe->setEmailAddressList();
        }
    }
}
