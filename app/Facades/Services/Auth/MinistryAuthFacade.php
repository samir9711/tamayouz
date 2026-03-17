<?php

namespace App\Facades\Services\Auth;

use Illuminate\Support\Facades\Facade;

class MinistryAuthFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'MinistryAuthService';
    }

}
