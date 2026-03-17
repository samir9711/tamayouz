<?php

namespace App\Http\Controllers\EstablishmentAccount;

use App\Facades\Services\EstablishmentAccount\EstablishmentAccountFacade;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FatherCrudController;
use App\Http\Requests\Model\StoreEstablishmentAccountRequest;
use Illuminate\Http\Request;

class EstablishmentAccountController extends FatherCrudController
{
    protected function setVariables() : void {
        $this->key = "establishment_account";
        $this->service = EstablishmentAccountFacade::class;
        $this->createRequest = StoreEstablishmentAccountRequest::class;
        $this->updateRequest = StoreEstablishmentAccountRequest::class;
    }
}