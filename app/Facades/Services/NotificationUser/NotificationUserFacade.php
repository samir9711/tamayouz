<?php

namespace App\Facades\Services\NotificationUser;

use Illuminate\Support\Facades\Facade;

class NotificationUserFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'NotificationUserService';
    }
}