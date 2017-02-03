<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\User;
use Vinkla\Pusher\Facades\Pusher;

class UpdateRoute extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    private $userId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = User::find($this->userId);
        $user->latitude = 1;
        $user->longitude = 2;

        $user->save();

        $event_name = $user->role === 'DRIVER'
            ? 'driver-location-changed'
            : 'client-location-changed';

        Pusher::trigger('tako-channel', $event_name, [strtolower($user->role) => $user]);
    }
}
