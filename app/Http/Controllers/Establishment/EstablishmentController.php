<?php

namespace App\Http\Controllers\Establishment;

use App\Facades\Services\Establishment\EstablishmentFacade;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FatherCrudController;
use App\Http\Requests\Model\StoreEstablishmentRequest;
use Illuminate\Http\Request;

class EstablishmentController extends FatherCrudController
{
    protected function setVariables() : void {
        $this->key = "establishment";
        $this->service = EstablishmentFacade::class;
        $this->createRequest = StoreEstablishmentRequest::class;
        $this->updateRequest = StoreEstablishmentRequest::class;
    }
}