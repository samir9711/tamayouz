<?php

namespace App\Http\Resources\Model;

use App\Models\MinistryAccount;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Basic\BasicResource;
use App\Services\Basic\ModelColumnsService;

class MinistryAccountResource extends BasicResource
{
    public function toArray(Request $request): array
    {
        $data= $this->initResource(
            ModelColumnsService::getServiceFor(
                MinistryAccount::class
            )
        );
        $data['ministry'] = $this->whenLoaded('ministry', function () {
            return $this->ministry ? $this->ministry->toArray() : null;
        });

        return $data;
    }

    protected function initResource($modelColumnsService): array
    {
        $this->result = parent::initResource($modelColumnsService);

        return array_merge($this->result, []);
    }
}
