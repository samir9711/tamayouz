<?php

namespace App\Http\Resources\Model;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Basic\BasicResource;
use App\Services\Basic\ModelColumnsService;

class UserResource extends BasicResource
{
    public function toArray(Request $request): array
    {
        $data= $this->initResource(
            ModelColumnsService::getServiceFor(
                User::class
            )
        );
        $data['directorate'] = $this->whenLoaded('directorate', function () {
            return $this->directorate ? $this->directorate->toArray() : null;
        });

        return $data;
    }

    protected function initResource($modelColumnsService): array
    {
        $this->result = parent::initResource($modelColumnsService);

        return array_merge($this->result, []);
    }
}
