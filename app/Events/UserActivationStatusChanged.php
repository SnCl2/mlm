<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserActivationStatusChanged
{
    use Dispatchable, SerializesModels;

    public $user;
    public $wasActive;
    public $isNowActive;

    public function __construct(User $user, bool $wasActive, bool $isNowActive)
    {
        $this->user = $user;
        $this->wasActive = $wasActive;
        $this->isNowActive = $isNowActive;
    }
}
