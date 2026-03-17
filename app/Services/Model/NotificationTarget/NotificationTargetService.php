<?php

namespace App\Services\Model\NotificationTarget;

use App\Services\Basic\BasicCrudService;
use App\Services\Basic\ModelColumnsService;
use App\Models\NotificationTarget;
use App\Http\Resources\Model\NotificationTargetResource;

class NotificationTargetService extends BasicCrudService
{
    /**
     * Override to set up modelColumnsService and resource.
     */
    protected function setVariables(): void
    {
        $this->modelColumnsService = ModelColumnsService::getServiceFor(
            $this->model = NotificationTarget::class
        );

        $this->resource = NotificationTargetResource::class;
    }
}