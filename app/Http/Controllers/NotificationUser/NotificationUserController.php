<?php

namespace App\Http\Controllers\NotificationUser;

use App\Facades\Services\NotificationUser\NotificationUserFacade;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FatherCrudController;
use App\Http\Requests\Model\StoreNotificationUserRequest;
use Illuminate\Http\Request;

class NotificationUserController extends FatherCrudController
{
    protected function setVariables() : void {
        $this->key = "notification_user";
        $this->service = NotificationUserFacade::class;
        $this->createRequest = StoreNotificationUserRequest::class;
        $this->updateRequest = StoreNotificationUserRequest::class;
    }
}