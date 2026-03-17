<?php

namespace App\Http\Resources\Model;

use App\Models\Ministry;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Basic\BasicResource;
use App\Services\Basic\ModelColumnsService;

class MinistryResource extends BasicResource
{
    public function toArray(Request $request): array
    {
        return $this->initResource(
            ModelColumnsService::getServiceFor(
                Ministry::class
            )
        );
    }

    protected function initResource($modelColumnsService): array
    {
        $this->result = parent::initResource($modelColumnsService);

        return array_merge($this->result, []);
    }
}