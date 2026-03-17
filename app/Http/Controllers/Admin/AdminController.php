<?php

namespace App\Http\Controllers\Admin;

use App\Facades\Services\Admin\AdminFacade;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FatherCrudController;
use App\Http\Requests\Model\StoreAdminRequest;
use App\Http\Requests\Model\UpdateAdminRequest;
use Illuminate\Http\Request;
use App\Services\Admin\AdminAccountService;
use Illuminate\Http\JsonResponse;

class AdminController extends FatherCrudController
{
    protected function setVariables() : void {
        $this->key = "admin";
        $this->service = AdminFacade::class;
        $this->createRequest = StoreAdminRequest::class;
        $this->updateRequest = StoreAdminRequest::class;
    }


    public function updateProfile(UpdateAdminRequest $request): JsonResponse
    {
        try {
            $result = $this->service::updateAuthenticatedAdmin($request);

            return response()->json([
                'data' => ['admin' => $result],
                'status' => true,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'data' => null,
                'status' => false,
                'error' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {

            return response()->json([
                'data' => null,
                'status' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
