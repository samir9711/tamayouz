<?php



use App\Http\Controllers\Auth\MinisrtyAuthController;
use App\Http\Controllers\Badge\BadgeController;
use App\Http\Controllers\Directorate\DirectorateController;
use App\Http\Controllers\Ministry\MinistryController;
use App\Http\Controllers\MinistryAccount\MinistryAccountController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('establishment')->group(function () {


    Route::post('/scan', [BadgeController::class, 'scan'])
    ->middleware('auth:establishment');




    Route::get('/scanned', [BadgeController::class, 'scannedByEstablishment'])
    ->middleware('auth:establishment');
});

