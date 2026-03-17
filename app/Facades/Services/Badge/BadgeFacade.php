<?php

namespace App\Facades\Services\Badge;

use Illuminate\Support\Facades\Facade;

class BadgeFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'BadgeService';
    }
}