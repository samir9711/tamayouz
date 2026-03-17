<?php

namespace App\Services\Model\EstablishmentAccount;

use App\Services\Basic\BasicCrudService;
use App\Services\Basic\ModelColumnsService;
use App\Models\EstablishmentAccount;
use App\Http\Resources\Model\EstablishmentAccountResource;

class EstablishmentAccountService extends BasicCrudService
{
    /**
     * Override to set up modelColumnsService and resource.
     */
    protected function setVariables(): void
    {
        $this->modelColumnsService = ModelColumnsService::getServiceFor(
            $this->model = EstablishmentAccount::class
        );

        $this->resource = EstablishmentAccountResource::class;
    }
}