<?php

namespace App\Http\Controllers\Notification;

use App\Facades\Services\Notification\NotificationFacade;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FatherCrudController;
use App\Http\Requests\Model\CreateNotificationRequest;
use App\Http\Requests\Model\StoreNotificationRequest;
use App\Services\Model\Notification\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends FatherCrudController
{
    protected function setVariables() : void {
        $this->key = "notification";
        $this->service = NotificationFacade::class;
        $this->createRequest = StoreNotificationRequest::class;
        $this->updateRequest = StoreNotificationRequest::class;
    }



    public function sendByMinistry(CreateNotificationRequest $request)
    {
        try {
            $sender = auth('ministry')->user();
            if (!$sender) {
                return $this->apiResponse(null, false, 'Unauthenticated.', 401);
            }

         

            $payload = $request->validated();

            $notif = NotificationService::sendFromMinistry($sender, $payload);

            return $this->apiResponse(['notification' => $notif], true);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }


    public function index(Request $request)
    {
        try {
            $user = auth('user')->user();
            if (!$user) {
                return $this->apiResponse(null, false, 'Unauthenticated.', 401);
            }

            $perPage = (int) $request->input('per_page', 15);
            $onlyUnread = filter_var($request->input('only_unread', false), FILTER_VALIDATE_BOOLEAN);

            $result = NotificationService::getForUser($user, $perPage, $onlyUnread);

            return $this->apiResponse($result);
        } catch (\Exception $e) {
            // حافظ على صيغة الأخطاء في مشروعك
            return $this->handleException($e);
        }
    }

}
