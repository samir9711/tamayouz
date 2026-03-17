<?php

namespace App\Facades\Services\Directorate;

use Illuminate\Support\Facades\Facade;

class DirectorateFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'DirectorateService';
    }
}