<?php

namespace App\Facades\Services\BadgeDiscount;

use Illuminate\Support\Facades\Facade;

class BadgeDiscountFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'BadgeDiscountService';
    }
}