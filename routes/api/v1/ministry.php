<?php



use App\Http\Controllers\Auth\MinisrtyAuthController;
use App\Http\Controllers\Badge\BadgeController;
use App\Http\Controllers\Directorate\DirectorateController;
use App\Http\Controllers\Ministry\MinistryController;
use App\Http\Controllers\MinistryAccount\MinistryAccountController;
use App\Http\Controllers\Notification\NotificationController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('ministry')->group(function () {

    Route::get('/ping', function () {
    return response()->json(['status' => 'ok']);
    });

    Route::post('login', [MinisrtyAuthController::class, 'login']);

    Route::middleware('auth:ministry')->group(function () {
        Route::post('logout', [MinisrtyAuthController::class, 'logout']);
    });


    Route::prefix('user')->middleware('auth:ministry')->group(function () {
        Route::get('/all/paginated', [UserController::class, 'allPaginated']);
        Route::get('/all',           [UserController::class, 'all']);
        Route::post('/show',         [UserController::class, 'show']);
        Route::post('/create',       [UserController::class, 'store']);
        Route::post('/update',       [UserController::class, 'update']);
        Route::delete('/destroy',    [UserController::class, 'destroy']);
    });


    Route::prefix('directorate')->middleware('auth:ministry')->group(function () {
        Route::get('/all/paginated', [DirectorateController::class, 'allPaginated']);
        Route::get('/all',           [DirectorateController::class, 'all']);
        Route::post('/show',         [DirectorateController::class, 'show']);
        Route::post('/create',       [DirectorateController::class, 'store']);
        Route::post('/update',       [DirectorateController::class, 'update']);
        Route::delete('/destroy',    [DirectorateController::class, 'destroy']);
    });

    Route::prefix('badge')->middleware('auth:ministry')->group(function () {
        Route::get('/all/paginated', [BadgeController::class, 'allPaginated']);
        Route::get('/all',           [BadgeController::class, 'all']);
        Route::post('/show',         [BadgeController::class, 'show']);
        Route::post('/create',       [BadgeController::class, 'store']);
        Route::delete('/destroy',    [BadgeController::class, 'destroy']);
    });



    Route::prefix('ministry-account')->middleware(['auth:ministry', 'min_admin_or_admin'])->group(function () {
        Route::get('/all/paginated', [MinistryAccountController::class, 'allPaginated']);
        Route::get('/all',           [MinistryAccountController::class, 'all']);
        Route::post('/show',         [MinistryAccountController::class, 'show']);
        Route::post('/create',       [MinistryAccountController::class, 'store']);
        Route::post('/update',       [MinistryAccountController::class, 'update']);
        Route::delete('/destroy',    [MinistryAccountController::class, 'destroy']);

    });

    Route::get('/my', [MinistryController::class, 'getByAuth'])->middleware('auth:ministry');
    Route::post('/update',       [MinistryController::class, 'update'])->middleware('auth:ministry');


    Route::post('notifications/send', [NotificationController::class, 'sendByMinistry'])
    ->middleware('auth:ministry');



});

