<?php

namespace App\Services\Model\PrivacyPolicy;

use App\Services\Basic\BasicCrudService;
use App\Services\Basic\ModelColumnsService;
use App\Models\PrivacyPolicy;
use App\Http\Resources\Model\PrivacyPolicyResource;

class PrivacyPolicyService extends BasicCrudService
{
    /**
     * Override to set up modelColumnsService and resource.
     */
    protected function setVariables(): void
    {
        $this->modelColumnsService = ModelColumnsService::getServiceFor(
            $this->model = PrivacyPolicy::class
        );

        $this->resource = PrivacyPolicyResource::class;
    }
}