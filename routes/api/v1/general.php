<?php

use App\Http\Controllers\AboutUs\AboutUsController;
use App\Http\Controllers\Auth\UnifiedAuthController;
use App\Http\Controllers\Directorate\DirectorateController;
use App\Http\Controllers\Establishment\EstablishmentController;
use App\Http\Controllers\Ministry\MinistryController;
use App\Http\Controllers\MinistryAccount\MinistryAccountController;
use App\Http\Controllers\PrivacyPolicy\PrivacyPolicyController;
use App\Http\Controllers\TermsCondition\TermsConditionController;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;


Route::post('upload/{folder}/single', [UploadController::class, 'single'])
    ->where('folder', '[A-Za-z0-9_-]+');

Route::post('upload/{folder}/multiple', [UploadController::class, 'multiple'])
    ->where('folder', '[A-Za-z0-9_-]+');

Route::post('general/login', [UnifiedAuthController::class, 'login']);

Route::prefix('about-us')->group(function () {

    Route::get('/all', [AboutUsController::class, 'all']);
});

Route::prefix('directorate')->group(function () {
    Route::get('/all/paginated', [DirectorateController::class, 'allPaginated']);
    Route::get('/all',           [DirectorateController::class, 'all']);
    Route::post('/show',         [DirectorateController::class, 'show']);
});


Route::prefix('establishment')->group(function () {
    Route::get('/all/paginated', [EstablishmentController::class, 'allPaginated']);
    Route::get('/all',           [EstablishmentController::class, 'all']);
    Route::post('/show',         [EstablishmentController::class, 'show']);

});

Route::prefix('ministry')->group(function () {
    Route::get('/all/paginated', [MinistryController::class, 'allPaginated']);
    Route::get('/all',           [MinistryController::class, 'all']);
    Route::post('/show',         [MinistryController::class, 'show']);
});


Route::prefix('privacy-policy')->group(function () {
    Route::get('/all/paginated', [PrivacyPolicyController::class, 'allPaginated']);
    Route::get('/all',           [PrivacyPolicyController::class, 'all']);
    Route::post('/show',         [PrivacyPolicyController::class, 'show']);
});

Route::prefix('terms-condition')->group(function () {
    Route::get('/all/paginated', [TermsConditionController::class, 'allPaginated']);
    Route::get('/all',           [TermsConditionController::class, 'all']);
    Route::post('/show',         [TermsConditionController::class, 'show']);

});


Route::prefix('faq')->group(function () {
        Route::get('/all/paginated', [\App\Http\Controllers\Faq\FaqController::class, 'allPaginated']);
        Route::get('/all',           [\App\Http\Controllers\Faq\FaqController::class, 'all']);
        Route::post('/show',         [\App\Http\Controllers\Faq\FaqController::class, 'show']);

});



