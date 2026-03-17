<?php

namespace App\Http\Controllers\BadgeDiscount;

use App\Facades\Services\BadgeDiscount\BadgeDiscountFacade;
use App\Http\Controllers\Controller;
use App\Http\Controllers\FatherCrudController;
use App\Http\Requests\Model\StoreBadgeDiscountRequest;
use Illuminate\Http\Request;

class BadgeDiscountController extends FatherCrudController
{
    protected function setVariables() : void {
        $this->key = "badge_discount";
        $this->service = BadgeDiscountFacade::class;
        $this->createRequest = StoreBadgeDiscountRequest::class;
        $this->updateRequest = StoreBadgeDiscountRequest::class;
    }
}