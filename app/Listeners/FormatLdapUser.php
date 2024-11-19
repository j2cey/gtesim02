<?php

namespace App\Listeners;

use App\Models\LdapUser;
use App\Events\LdapUserSaved;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class FormatLdapUser
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(LdapUserSaved $event): void
    {
        $ldap_user = LdapUser::find($event->ldap_user_id);

        $ldap_user->formatTitle();
        $ldap_user->formatDepartment();
    }
}
