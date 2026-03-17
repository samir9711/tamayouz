<?php

namespace App\Http\Controllers\MinistryAccount;

use App\Facades\Services\MinistryAccount\MinistryAccountFacade;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FatherCrudController;
use App\Http\Requests\Model\StoreMinistryAccountRequest;
use Illuminate\Http\Request;

class MinistryAccountController extends FatherCrudController
{
    protected function setVariables() : void {
        $this->key = "ministry_account";
        $this->service = MinistryAccountFacade::class;
        $this->createRequest = StoreMinistryAccountRequest::class;
        $this->updateRequest = StoreMinistryAccountRequest::class;
    }
}