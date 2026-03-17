<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// AboutUs PUBLIC ROUTES
/*
Route::prefix('about-us')->group(function () {
    Route::get('/all/paginated', [\App\Http\Controllers\AboutUs\AboutUsController::class, 'allPaginated']);
    Route::get('/all',           [\App\Http\Controllers\AboutUs\AboutUsController::class, 'all']);
    Route::post('/show',         [\App\Http\Controllers\AboutUs\AboutUsController::class, 'show']);
    Route::post('/create',       [\App\Http\Controllers\AboutUs\AboutUsController::class, 'store']);
    Route::post('/update',       [\App\Http\Controllers\AboutUs\AboutUsController::class, 'update']);
    Route::post('/activate',     [\App\Http\Controllers\AboutUs\AboutUsController::class, 'activate']);
    Route::post('/deactivate',   [\App\Http\Controllers\AboutUs\AboutUsController::class, 'deactivate']);
});

// Admin PUBLIC ROUTES
Route::prefix('admin')->group(function () {
    Route::get('/all/paginated', [\App\Http\Controllers\Admin\AdminController::class, 'allPaginated']);
    Route::get('/all',           [\App\Http\Controllers\Admin\AdminController::class, 'all']);
    Route::post('/show',         [\App\Http\Controllers\Admin\AdminController::class, 'show']);
    Route::post('/create',       [\App\Http\Controllers\Admin\AdminController::class, 'store']);
    Route::post('/update',       [\App\Http\Controllers\Admin\AdminController::class, 'update']);
    Route::post('/activate',     [\App\Http\Controllers\Admin\AdminController::class, 'activate']);
    Route::post('/deactivate',   [\App\Http\Controllers\Admin\AdminController::class, 'deactivate']);
});

// Badge PUBLIC ROUTES
Route::prefix('badge')->group(function () {
    Route::get('/all/paginated', [\App\Http\Controllers\Badge\BadgeController::class, 'allPaginated']);
    Route::get('/all',           [\App\Http\Controllers\Badge\BadgeController::class, 'all']);
    Route::post('/show',         [\App\Http\Controllers\Badge\BadgeController::class, 'show']);
    Route::post('/create',       [\App\Http\Controllers\Badge\BadgeController::class, 'store']);
    Route::post('/update',       [\App\Http\Controllers\Badge\BadgeController::class, 'update']);
    Route::post('/activate',     [\App\Http\Controllers\Badge\BadgeController::class, 'activate']);
    Route::post('/deactivate',   [\App\Http\Controllers\Badge\BadgeController::class, 'deactivate']);
});

// BadgeDiscount PUBLIC ROUTES
Route::prefix('badge-discount')->group(function () {
    Route::get('/all/paginated', [\App\Http\Controllers\BadgeDiscount\BadgeDiscountController::class, 'allPaginated']);
    Route::get('/all',           [\App\Http\Controllers\BadgeDiscount\BadgeDiscountController::class, 'all']);
    Route::post('/show',         [\App\Http\Controllers\BadgeDiscount\BadgeDiscountController::class, 'show']);
    Route::post('/create',       [\App\Http\Controllers\BadgeDiscount\BadgeDiscountController::class, 'store']);
    Route::post('/update',       [\App\Http\Controllers\BadgeDiscount\BadgeDiscountController::class, 'update']);
    Route::post('/activate',     [\App\Http\Controllers\BadgeDiscount\BadgeDiscountController::class, 'activate']);
    Route::post('/deactivate',   [\App\Http\Controllers\BadgeDiscount\BadgeDiscountController::class, 'deactivate']);
});

// Directorate PUBLIC ROUTES
Route::prefix('directorate')->group(function () {
    Route::get('/all/paginated', [\App\Http\Controllers\Directorate\DirectorateController::class, 'allPaginated']);
    Route::get('/all',           [\App\Http\Controllers\Directorate\DirectorateController::class, 'all']);
    Route::post('/show',         [\App\Http\Controllers\Directorate\DirectorateController::class, 'show']);
    Route::post('/create',       [\App\Http\Controllers\Directorate\DirectorateController::class, 'store']);
    Route::post('/update',       [\App\Http\Controllers\Directorate\DirectorateController::class, 'update']);
    Route::post('/activate',     [\App\Http\Controllers\Directorate\DirectorateController::class, 'activate']);
    Route::post('/deactivate',   [\App\Http\Controllers\Directorate\DirectorateController::class, 'deactivate']);
});

// Establishment PUBLIC ROUTES
Route::prefix('establishment')->group(function () {
    Route::get('/all/paginated', [\App\Http\Controllers\Establishment\EstablishmentController::class, 'allPaginated']);
    Route::get('/all',           [\App\Http\Controllers\Establishment\EstablishmentController::class, 'all']);
    Route::post('/show',         [\App\Http\Controllers\Establishment\EstablishmentController::class, 'show']);
    Route::post('/create',       [\App\Http\Controllers\Establishment\EstablishmentController::class, 'store']);
    Route::post('/update',       [\App\Http\Controllers\Establishment\EstablishmentController::class, 'update']);
    Route::post('/activate',     [\App\Http\Controllers\Establishment\EstablishmentController::class, 'activate']);
    Route::post('/deactivate',   [\App\Http\Controllers\Establishment\EstablishmentController::class, 'deactivate']);
});

// EstablishmentAccount PUBLIC ROUTES
Route::prefix('establishment-account')->group(function () {
    Route::get('/all/paginated', [\App\Http\Controllers\EstablishmentAccount\EstablishmentAccountController::class, 'allPaginated']);
    Route::get('/all',           [\App\Http\Controllers\EstablishmentAccount\EstablishmentAccountController::class, 'all']);
    Route::post('/show',         [\App\Http\Controllers\EstablishmentAccount\EstablishmentAccountController::class, 'show']);
    Route::post('/create',       [\App\Http\Controllers\EstablishmentAccount\EstablishmentAccountController::class, 'store']);
    Route::post('/update',       [\App\Http\Controllers\EstablishmentAccount\EstablishmentAccountController::class, 'update']);
    Route::post('/activate',     [\App\Http\Controllers\EstablishmentAccount\EstablishmentAccountController::class, 'activate']);
    Route::post('/deactivate',   [\App\Http\Controllers\EstablishmentAccount\EstablishmentAccountController::class, 'deactivate']);
});

// Ministry PUBLIC ROUTES
Route::prefix('ministry')->group(function () {
    Route::get('/all/paginated', [\App\Http\Controllers\Ministry\MinistryController::class, 'allPaginated']);
    Route::get('/all',           [\App\Http\Controllers\Ministry\MinistryController::class, 'all']);
    Route::post('/show',         [\App\Http\Controllers\Ministry\MinistryController::class, 'show']);
    Route::post('/create',       [\App\Http\Controllers\Ministry\MinistryController::class, 'store']);
    Route::post('/update',       [\App\Http\Controllers\Ministry\MinistryController::class, 'update']);
    Route::post('/activate',     [\App\Http\Controllers\Ministry\MinistryController::class, 'activate']);
    Route::post('/deactivate',   [\App\Http\Controllers\Ministry\MinistryController::class, 'deactivate']);
});

// MinistryAccount PUBLIC ROUTES
Route::prefix('ministry-account')->group(function () {
    Route::get('/all/paginated', [\App\Http\Controllers\MinistryAccount\MinistryAccountController::class, 'allPaginated']);
    Route::get('/all',           [\App\Http\Controllers\MinistryAccount\MinistryAccountController::class, 'all']);
    Route::post('/show',         [\App\Http\Controllers\MinistryAccount\MinistryAccountController::class, 'show']);
    Route::post('/create',       [\App\Http\Controllers\MinistryAccount\MinistryAccountController::class, 'store']);
    Route::post('/update',       [\App\Http\Controllers\MinistryAccount\MinistryAccountController::class, 'update']);
    Route::post('/activate',     [\App\Http\Controllers\MinistryAccount\MinistryAccountController::class, 'activate']);
    Route::post('/deactivate',   [\App\Http\Controllers\MinistryAccount\MinistryAccountController::class, 'deactivate']);
});

// Notification PUBLIC ROUTES
Route::prefix('notification')->group(function () {
    Route::get('/all/paginated', [\App\Http\Controllers\Notification\NotificationController::class, 'allPaginated']);
    Route::get('/all',           [\App\Http\Controllers\Notification\NotificationController::class, 'all']);
    Route::post('/show',         [\App\Http\Controllers\Notification\NotificationController::class, 'show']);
    Route::post('/create',       [\App\Http\Controllers\Notification\NotificationController::class, 'store']);
    Route::post('/update',       [\App\Http\Controllers\Notification\NotificationController::class, 'update']);
    Route::post('/activate',     [\App\Http\Controllers\Notification\NotificationController::class, 'activate']);
    Route::post('/deactivate',   [\App\Http\Controllers\Notification\NotificationController::class, 'deactivate']);
});

// NotificationTarget PUBLIC ROUTES
Route::prefix('notification-target')->group(function () {
    Route::get('/all/paginated', [\App\Http\Controllers\NotificationTarget\NotificationTargetController::class, 'allPaginated']);
    Route::get('/all',           [\App\Http\Controllers\NotificationTarget\NotificationTargetController::class, 'all']);
    Route::post('/show',         [\App\Http\Controllers\NotificationTarget\NotificationTargetController::class, 'show']);
    Route::post('/create',       [\App\Http\Controllers\NotificationTarget\NotificationTargetController::class, 'store']);
    Route::post('/update',       [\App\Http\Controllers\NotificationTarget\NotificationTargetController::class, 'update']);
    Route::post('/activate',     [\App\Http\Controllers\NotificationTarget\NotificationTargetController::class, 'activate']);
    Route::post('/deactivate',   [\App\Http\Controllers\NotificationTarget\NotificationTargetController::class, 'deactivate']);
});

// NotificationUser PUBLIC ROUTES
Route::prefix('notification-user')->group(function () {
    Route::get('/all/paginated', [\App\Http\Controllers\NotificationUser\NotificationUserController::class, 'allPaginated']);
    Route::get('/all',           [\App\Http\Controllers\NotificationUser\NotificationUserController::class, 'all']);
    Route::post('/show',         [\App\Http\Controllers\NotificationUser\NotificationUserController::class, 'show']);
    Route::post('/create',       [\App\Http\Controllers\NotificationUser\NotificationUserController::class, 'store']);
    Route::post('/update',       [\App\Http\Controllers\NotificationUser\NotificationUserController::class, 'update']);
    Route::post('/activate',     [\App\Http\Controllers\NotificationUser\NotificationUserController::class, 'activate']);
    Route::post('/deactivate',   [\App\Http\Controllers\NotificationUser\NotificationUserController::class, 'deactivate']);
});

// Otp PUBLIC ROUTES
Route::prefix('otp')->group(function () {
    Route::get('/all/paginated', [\App\Http\Controllers\Otp\OtpController::class, 'allPaginated']);
    Route::get('/all',           [\App\Http\Controllers\Otp\OtpController::class, 'all']);
    Route::post('/show',         [\App\Http\Controllers\Otp\OtpController::class, 'show']);
    Route::post('/create',       [\App\Http\Controllers\Otp\OtpController::class, 'store']);
    Route::post('/update',       [\App\Http\Controllers\Otp\OtpController::class, 'update']);
    Route::post('/activate',     [\App\Http\Controllers\Otp\OtpController::class, 'activate']);
    Route::post('/deactivate',   [\App\Http\Controllers\Otp\OtpController::class, 'deactivate']);
});

// PrivacyPolicy PUBLIC ROUTES
Route::prefix('privacy-policy')->group(function () {
    Route::get('/all/paginated', [\App\Http\Controllers\PrivacyPolicy\PrivacyPolicyController::class, 'allPaginated']);
    Route::get('/all',           [\App\Http\Controllers\PrivacyPolicy\PrivacyPolicyController::class, 'all']);
    Route::post('/show',         [\App\Http\Controllers\PrivacyPolicy\PrivacyPolicyController::class, 'show']);
    Route::post('/create',       [\App\Http\Controllers\PrivacyPolicy\PrivacyPolicyController::class, 'store']);
    Route::post('/update',       [\App\Http\Controllers\PrivacyPolicy\PrivacyPolicyController::class, 'update']);
    Route::post('/activate',     [\App\Http\Controllers\PrivacyPolicy\PrivacyPolicyController::class, 'activate']);
    Route::post('/deactivate',   [\App\Http\Controllers\PrivacyPolicy\PrivacyPolicyController::class, 'deactivate']);
});

// Role PUBLIC ROUTES
Route::prefix('role')->group(function () {
    Route::get('/all/paginated', [\App\Http\Controllers\Role\RoleController::class, 'allPaginated']);
    Route::get('/all',           [\App\Http\Controllers\Role\RoleController::class, 'all']);
    Route::post('/show',         [\App\Http\Controllers\Role\RoleController::class, 'show']);
    Route::post('/create',       [\App\Http\Controllers\Role\RoleController::class, 'store']);
    Route::post('/update',       [\App\Http\Controllers\Role\RoleController::class, 'update']);
    Route::post('/activate',     [\App\Http\Controllers\Role\RoleController::class, 'activate']);
    Route::post('/deactivate',   [\App\Http\Controllers\Role\RoleController::class, 'deactivate']);
});

// Setting PUBLIC ROUTES
Route::prefix('setting')->group(function () {
    Route::get('/all/paginated', [\App\Http\Controllers\Setting\SettingController::class, 'allPaginated']);
    Route::get('/all',           [\App\Http\Controllers\Setting\SettingController::class, 'all']);
    Route::post('/show',         [\App\Http\Controllers\Setting\SettingController::class, 'show']);
    Route::post('/create',       [\App\Http\Controllers\Setting\SettingController::class, 'store']);
    Route::post('/update',       [\App\Http\Controllers\Setting\SettingController::class, 'update']);
    Route::post('/activate',     [\App\Http\Controllers\Setting\SettingController::class, 'activate']);
    Route::post('/deactivate',   [\App\Http\Controllers\Setting\SettingController::class, 'deactivate']);
});

// TermsCondition PUBLIC ROUTES
Route::prefix('terms-condition')->group(function () {
    Route::get('/all/paginated', [\App\Http\Controllers\TermsCondition\TermsConditionController::class, 'allPaginated']);
    Route::get('/all',           [\App\Http\Controllers\TermsCondition\TermsConditionController::class, 'all']);
    Route::post('/show',         [\App\Http\Controllers\TermsCondition\TermsConditionController::class, 'show']);
    Route::post('/create',       [\App\Http\Controllers\TermsCondition\TermsConditionController::class, 'store']);
    Route::post('/update',       [\App\Http\Controllers\TermsCondition\TermsConditionController::class, 'update']);
    Route::post('/activate',     [\App\Http\Controllers\TermsCondition\TermsConditionController::class, 'activate']);
    Route::post('/deactivate',   [\App\Http\Controllers\TermsCondition\TermsConditionController::class, 'deactivate']);
});

// User PUBLIC ROUTES
Route::prefix('user')->group(function () {
    Route::get('/all/paginated', [\App\Http\Controllers\User\UserController::class, 'allPaginated']);
    Route::get('/all',           [\App\Http\Controllers\User\UserController::class, 'all']);
    Route::post('/show',         [\App\Http\Controllers\User\UserController::class, 'show']);
    Route::post('/create',       [\App\Http\Controllers\User\UserController::class, 'store']);
    Route::post('/update',       [\App\Http\Controllers\User\UserController::class, 'update']);
    Route::post('/activate',     [\App\Http\Controllers\User\UserController::class, 'activate']);
    Route::post('/deactivate',   [\App\Http\Controllers\User\UserController::class, 'deactivate']);
});
*/

// Faq PUBLIC ROUTES

