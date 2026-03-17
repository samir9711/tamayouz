<?php

namespace App\Services\Model\AboutUs;

use App\Services\Basic\BasicCrudService;
use App\Services\Basic\ModelColumnsService;
use App\Models\AboutUs;
use App\Http\Resources\Model\AboutUsResource;

class AboutUsService extends BasicCrudService
{
    /**
     * Override to set up modelColumnsService and resource.
     */
    protected function setVariables(): void
    {
        $this->modelColumnsService = ModelColumnsService::getServiceFor(
            $this->model = AboutUs::class
        );

        $this->resource = AboutUsResource::class;
    }
}