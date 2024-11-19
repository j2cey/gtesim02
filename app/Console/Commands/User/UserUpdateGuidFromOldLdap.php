<?php

namespace App\Console\Commands\User;

use App\Models\User;
use App\Models\LdapUser;
use Illuminate\Console\Command;

class UserUpdateGuidFromOldLdap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:guid-updatefromoldldap';

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
        $users = User::with('ldapaccount')->get();
        foreach ($users as $user) {
            if ( $this->setGuid($user) ) {
                $user->ldapAssociate(true);
            }
        }
    }

    private function setGuid(User $user): bool
    {
        $guid = null;
        if (is_null($user->objectguid) ) {
            $ldapuser = LdapUser::whereEmail($user->email)->first();
            if ($ldapuser) {
                $guid = $ldapuser->guid;
            }
        } else {
            $guid = $user->objectguid;
        }
        if ( ! is_null($guid) ) {
            $user->guid = $guid;
            $user->save();

            return true;
        } else {
            return false;
        }
    }
}
