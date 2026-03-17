<?php

namespace App\Http\Resources\Model;

use App\Models\Badge;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Basic\BasicResource;
use App\Services\Basic\ModelColumnsService;

class BadgeResource extends BasicResource
{
    public function toArray(Request $request): array
    {
       $data= $this->initResource(
            ModelColumnsService::getServiceFor(
                Badge::class
            )
        );
        $data['ministry'] = $this->whenLoaded('ministry', function () {
            return $this->ministry ? $this->ministry->toArray() : null;
        });
        $data['user'] = $this->whenLoaded('user', function () {
            return $this->user ? $this->user->toArray() : null;
        });
        return $data;
    }

    protected function initResource($modelColumnsService): array
    {
        $this->result = parent::initResource($modelColumnsService);


        $discounts = $this->resource->relationLoaded('discounts')
            ? $this->resource->getRelation('discounts')
            : $this->resource->discounts()->get();

        $this->result['discounts'] = BadgeDiscountResource::collection($discounts);

        return array_merge($this->result, []);
    }
}
