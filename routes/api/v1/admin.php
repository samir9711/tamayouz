<?php



use App\Http\Controllers\AboutUs\AboutUsController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Establishment\EstablishmentController;
use App\Http\Controllers\Faq\FaqController;
use App\Http\Controllers\Ministry\MinistryController;
use App\Http\Controllers\PrivacyPolicy\PrivacyPolicyController;
use App\Http\Controllers\TermsCondition\TermsConditionController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {

    Route::get('/ping', function () {
    return response()->json(['status' => 'ok']);
    });

    Route::post('login', [AdminAuthController::class, 'login']);

    Route::middleware('auth:admin')->group(function () {
        Route::post('logout', [AdminAuthController::class, 'logout']);
        Route::post('/profile/update', [AdminController::class, 'updateProfile']);
    });

    Route::prefix('ministry')->group(function () {
        Route::get('/all/paginated', [MinistryController::class, 'allPaginated']);
        Route::get('/all',           [MinistryController::class, 'all']);
        Route::post('/show',         [MinistryController::class, 'show']);
        Route::post('/create',       [MinistryController::class, 'store']);
        Route::post('/update',       [MinistryController::class, 'update']);
        Route::post('/activate',     [MinistryController::class, 'activate']);
        Route::post('/deactivate',   [MinistryController::class, 'deactivate']);
        Route::delete('/destroy',    [MinistryController::class, 'destroy']);
    });






    Route::prefix('establishment')->middleware('auth:admin')->group(function () {
        Route::get('/all/paginated', [EstablishmentController::class, 'allPaginated']);
        Route::get('/all',           [EstablishmentController::class, 'all']);
        Route::post('/show',         [EstablishmentController::class, 'show']);
        Route::post('/create',       [EstablishmentController::class, 'store']);
        Route::post('/update',       [EstablishmentController::class, 'update']);
        Route::post('/activate',     [EstablishmentController::class, 'activate']);
        Route::post('/deactivate',   [EstablishmentController::class, 'deactivate']);
        Route::post('/destroy',      [EstablishmentController::class, 'destroy']);
    });




    Route::prefix('faq')->middleware('auth:admin')->group(function () {
        Route::get('/all/paginated', [FaqController::class, 'allPaginated']);
        Route::get('/all',           [FaqController::class, 'all']);
        Route::post('/show',         [FaqController::class, 'show']);
        Route::post('/create',       [FaqController::class, 'store']);
        Route::post('/update',       [FaqController::class, 'update']);
        Route::post('/activate',     [FaqController::class, 'activate']);
        Route::post('/deactivate',   [FaqController::class, 'deactivate']);
        Route::post('/destroy',      [FaqController::class, 'destroy']);
    });


    Route::prefix('about-us')->middleware('auth:admin')->group(function () {
        Route::get('/all/paginated', [AboutUsController::class, 'allPaginated']);
        Route::get('/all',           [AboutUsController::class, 'all']);
        Route::post('/show',         [AboutUsController::class, 'show']);
        Route::post('/create',       [AboutUsController::class, 'store']);
        Route::post('/update',       [AboutUsController::class, 'update']);
        Route::post('/activate',     [AboutUsController::class, 'activate']);
        Route::post('/deactivate',   [AboutUsController::class, 'deactivate']);
    });


    Route::prefix('privacy-policy')->middleware('auth:admin')->group(function () {
        Route::get('/all/paginated', [PrivacyPolicyController::class, 'allPaginated']);
        Route::get('/all',           [PrivacyPolicyController::class, 'all']);
        Route::post('/show',         [PrivacyPolicyController::class, 'show']);
        Route::post('/create',       [PrivacyPolicyController::class, 'store']);
        Route::post('/update',       [PrivacyPolicyController::class, 'update']);
        Route::post('/activate',     [PrivacyPolicyController::class, 'activate']);
        Route::post('/deactivate',   [PrivacyPolicyController::class, 'deactivate']);
    });



    Route::prefix('terms-condition')->middleware('auth:admin')->group(function () {
        Route::get('/all/paginated', [TermsConditionController::class, 'allPaginated']);
        Route::get('/all',           [TermsConditionController::class, 'all']);
        Route::post('/show',         [TermsConditionController::class, 'show']);
        Route::post('/create',       [TermsConditionController::class, 'store']);
        Route::post('/update',       [TermsConditionController::class, 'update']);
        Route::post('/activate',     [TermsConditionController::class, 'activate']);
        Route::post('/deactivate',   [TermsConditionController::class, 'deactivate']);
    });

});

