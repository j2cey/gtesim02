<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Events\LdapUserSaved;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LdapUserSavedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $ldap_user_id;

    /**
     * Create a new job instance.
     */
    public function __construct($ldap_user_id)
    {
        $this->ldap_user_id = $ldap_user_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        event( new LdapUserSaved($this->ldap_user_id) );
    }
}
