<?php

namespace App\Http\Resources\Model;

use App\Models\BadgeDiscount;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Basic\BasicResource;
use App\Services\Basic\ModelColumnsService;

class BadgeDiscountResource extends BasicResource
{
    public function toArray(Request $request): array
    {
        $data= $this->initResource(
            ModelColumnsService::getServiceFor(
                BadgeDiscount::class
            )
        );
        $data['establishment'] = $this->whenLoaded('establishment', function () {
            return $this->establishment ? $this->establishment->toArray() : null;
        });



        return $data;
    }

    protected function initResource($modelColumnsService): array
    {
        $this->result = parent::initResource($modelColumnsService);

        return array_merge($this->result, []);
    }
}
