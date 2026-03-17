<?php

namespace App\Services\Model\NotificationUser;

use App\Services\Basic\BasicCrudService;
use App\Services\Basic\ModelColumnsService;
use App\Models\NotificationUser;
use App\Http\Resources\Model\NotificationUserResource;

class NotificationUserService extends BasicCrudService
{
    /**
     * Override to set up modelColumnsService and resource.
     */
    protected function setVariables(): void
    {
        $this->modelColumnsService = ModelColumnsService::getServiceFor(
            $this->model = NotificationUser::class
        );

        $this->resource = NotificationUserResource::class;
    }
}