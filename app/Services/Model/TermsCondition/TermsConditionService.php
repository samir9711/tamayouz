<?php

namespace App\Services\Model\TermsCondition;

use App\Services\Basic\BasicCrudService;
use App\Services\Basic\ModelColumnsService;
use App\Models\TermsCondition;
use App\Http\Resources\Model\TermsConditionResource;

class TermsConditionService extends BasicCrudService
{
    /**
     * Override to set up modelColumnsService and resource.
     */
    protected function setVariables(): void
    {
        $this->modelColumnsService = ModelColumnsService::getServiceFor(
            $this->model = TermsCondition::class
        );

        $this->resource = TermsConditionResource::class;
    }
}