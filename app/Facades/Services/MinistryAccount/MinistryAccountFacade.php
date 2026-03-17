<?php

namespace App\Facades\Services\MinistryAccount;

use Illuminate\Support\Facades\Facade;

class MinistryAccountFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'MinistryAccountService';
    }
}