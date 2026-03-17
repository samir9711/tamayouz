<?php

namespace App\Http\Controllers\NotificationTarget;

use App\Facades\Services\NotificationTarget\NotificationTargetFacade;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FatherCrudController;
use App\Http\Requests\Model\StoreNotificationTargetRequest;
use Illuminate\Http\Request;

class NotificationTargetController extends FatherCrudController
{
    protected function setVariables() : void {
        $this->key = "notification_target";
        $this->service = NotificationTargetFacade::class;
        $this->createRequest = StoreNotificationTargetRequest::class;
        $this->updateRequest = StoreNotificationTargetRequest::class;
    }
}