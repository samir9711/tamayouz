<?php

namespace App\Services\Model\BadgeDiscount;

use App\Services\Basic\BasicCrudService;
use App\Services\Basic\ModelColumnsService;
use App\Models\BadgeDiscount;
use App\Http\Resources\Model\BadgeDiscountResource;

class BadgeDiscountService extends BasicCrudService
{
    /**
     * Override to set up modelColumnsService and resource.
     */
    protected function setVariables(): void
    {
        $this->modelColumnsService = ModelColumnsService::getServiceFor(
            $this->model = BadgeDiscount::class
        );

        $this->resource = BadgeDiscountResource::class;
        $this->relations = ['establishment', 'badge', 'badge.user', 'badge.ministry'];
    }
}
