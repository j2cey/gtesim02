<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use App\Events\UserActivatedEvent;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ManageUserActivated implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $user_id;
    public ?string $pwd;

    /**
     * Create a new job instance.
     */
    public function __construct(User $user, string $pwd = null)
    {
        $this->user_id = $user->id;
        $this->pwd = $pwd;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        event( new UserActivatedEvent( User::getById($this->user_id), $this->pwd ) );
    }
}
