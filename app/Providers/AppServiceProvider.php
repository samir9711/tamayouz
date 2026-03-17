<?php

namespace App\Providers;

use App\Services\Functional\AdminAuthService;
use App\Services\Functional\MinistryAuthService;
use App\Services\Functional\UserAuthService;
use Illuminate\Support\ServiceProvider;
use App\Exceptions\Handler;

class AppServiceProvider extends ServiceProvider
{

     protected $facades = [
    'FaqService' => \App\Services\Model\Faq\FaqService::class,

    'UserService' => \App\Services\Model\User\UserService::class,

    'TermsConditionService' => \App\Services\Model\TermsCondition\TermsConditionService::class,

    'SettingService' => \App\Services\Model\Setting\SettingService::class,

    'RoleService' => \App\Services\Model\Role\RoleService::class,

    'PrivacyPolicyService' => \App\Services\Model\PrivacyPolicy\PrivacyPolicyService::class,

    'OtpService' => \App\Services\Model\Otp\OtpService::class,

    'NotificationUserService' => \App\Services\Model\NotificationUser\NotificationUserService::class,

    'NotificationTargetService' => \App\Services\Model\NotificationTarget\NotificationTargetService::class,

    'NotificationService' => \App\Services\Model\Notification\NotificationService::class,

    'MinistryAccountService' => \App\Services\Model\MinistryAccount\MinistryAccountService::class,

    'MinistryService' => \App\Services\Model\Ministry\MinistryService::class,

    'EstablishmentAccountService' => \App\Services\Model\EstablishmentAccount\EstablishmentAccountService::class,

    'EstablishmentService' => \App\Services\Model\Establishment\EstablishmentService::class,

    'DirectorateService' => \App\Services\Model\Directorate\DirectorateService::class,

    'BadgeDiscountService' => \App\Services\Model\BadgeDiscount\BadgeDiscountService::class,

    'BadgeService' => \App\Services\Model\Badge\BadgeService::class,

    'AdminService' => \App\Services\Model\Admin\AdminService::class,

    'AboutUsService' => \App\Services\Model\AboutUs\AboutUsService::class,

    'AdminAuthService' => AdminAuthService::class,

    'UserAuthService' => UserAuthService::class,

    'MinistryAuthService' => MinistryAuthService::class,



     ];
    /**
     * Register any application services.
     */
    public function register(): void
    {
        foreach ($this->facades as $facade => $service) {
            $this->app->singleton($facade, function ($app) use ($service) {
                return $app->make($service);
            });
        }

        $this->app->singleton(\Illuminate\Contracts\Debug\ExceptionHandler::class, Handler::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
