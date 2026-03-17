<?php

namespace App\Facades\Services\NotificationTarget;

use Illuminate\Support\Facades\Facade;

class NotificationTargetFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'NotificationTargetService';
    }
}