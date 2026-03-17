<?php

namespace App\Http\Controllers\Badge;

use App\Facades\Services\Badge\BadgeFacade;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FatherCrudController;
use App\Http\Requests\Model\StoreBadgeRequest;
use App\Http\Requests\Model\ScanBadgeRequest;
use Illuminate\Http\Request;

class BadgeController extends FatherCrudController
{
    protected function setVariables() : void {
        $this->key = "badge";
        $this->service = BadgeFacade::class;
        $this->createRequest = StoreBadgeRequest::class;
        $this->updateRequest = StoreBadgeRequest::class;
    }


    public function scan(ScanBadgeRequest $request)
    {
        try {
            $data = $request->validated();

            $establishmentId = $data['establishment_id'] ?? null;
            if ($establishmentId === null && auth('establishment')->check()) {
                $establishmentId = auth('establishment')->user()->id;
            }


            $result = $this->service::scanQr($data['code'], $establishmentId, auth()->user() ?? null);

            return response()->json([
                'data' => $result,
                'status' => true
            ], 200);

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }


    public function scannedByEstablishment(Request $request)
    {
        try {
            $account = auth('establishment')->user();
            if (!$account) {
                return $this->apiResponse(null, false, 'Unauthenticated.', 401);
            }

            $establishmentId = $account->establishment_id ?? $account->id;

            $perPage = (int) $request->input('per_page', 15);


            $result = $this->service::getScannedByEstablishment($establishmentId, $perPage);

            return $this->apiResponse($result);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

}
