<?php

namespace App\Http\Controllers\Directorate;

use App\Facades\Services\Directorate\DirectorateFacade;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FatherCrudController;
use App\Http\Requests\Model\StoreDirectorateRequest;
use Illuminate\Http\Request;

class DirectorateController extends FatherCrudController
{
    protected function setVariables() : void {
        $this->key = "directorate";
        $this->service = DirectorateFacade::class;
        $this->createRequest = StoreDirectorateRequest::class;
        $this->updateRequest = StoreDirectorateRequest::class;
    }
}