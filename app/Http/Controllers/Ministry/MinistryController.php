<?php

namespace App\Http\Controllers\Ministry;

use App\Facades\Services\Ministry\MinistryFacade;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FatherCrudController;
use App\Http\Requests\Model\StoreMinistryRequest;
use App\Services\Model\Ministry\MinistryService;
use Illuminate\Http\Request;

class MinistryController extends FatherCrudController
{
    protected function setVariables() : void {
        $this->key = "ministry";
        $this->service = MinistryFacade::class;
        $this->createRequest = StoreMinistryRequest::class;
        $this->updateRequest = StoreMinistryRequest::class;
    }



    public function getByAuth(Request $request)
    {
        try {
            $svc = app(MinistryService::class);
            $data = $svc->getByAuth($request);
            return $this->apiResponse($data);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
}
