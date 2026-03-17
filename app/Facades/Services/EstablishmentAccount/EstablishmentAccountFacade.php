<?php

namespace App\Facades\Services\EstablishmentAccount;

use Illuminate\Support\Facades\Facade;

class EstablishmentAccountFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'EstablishmentAccountService';
    }
}