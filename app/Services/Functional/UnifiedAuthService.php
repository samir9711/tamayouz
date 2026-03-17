<?php
namespace App\Services\Functional;

use App\Http\Resources\Model\UserResource;
use App\Models\EstablishmentAccount;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Hash;

class UnifiedAuthService
{
    /**
     * محاولات الدخول المتعدّدة:
     * - الحالة الأولى: social login (social_id only)
     * - وإلا email+password
     * - وإلا phone+password (prefix_phone + phone_number)
     *
     * تجربة البحث: establishment ثم user (تغيّر الترتيب إن أردت)
     *
     * تُرجع مصفوفة: ['token' => ..., 'guard' => 'establishment'|'user', 'user' => resource|model]
     */
    public function login($request): array
    {
        $email  = $request->input('email');
        $pass   = $request->input('password');
        $pp     = $request->input('prefix_phone');
        $pn     = $request->input('phone_number');
        $social = $request->input('social_id');
        $device = $request->input('token_device') ?? 'api';

        // helper closure to check model credentials
        $checkModel = function ($modelInstance) use ($pass, $social) {
            if (!$modelInstance) return false;
            // social login
            if ($social && $modelInstance->social_id && (string)$modelInstance->social_id === (string)$social) {
                return true;
            }
            // password login
            if ($pass && $modelInstance->password && Hash::check($pass, $modelInstance->password)) {
                return true;
            }
            return false;
        };

        // 1) Try EstablishmentAccount (by social / email / phone)
        $est = null;
        if ($social) {
            $est = EstablishmentAccount::where('social_id', $social)->first();
        } elseif ($email) {
            $est = EstablishmentAccount::where('email', $email)->first();
        } elseif ($pp && $pn) {
            $est = EstablishmentAccount::where('prefix_phone', $pp)->where('phone_number', $pn)->first();
        }

        if ($est && $checkModel($est)) {
            // issue token
            $plain = $est->createToken($device)->plainTextToken;
            // build response user resource (if you have resource for establishment use it)
            $userPayload = method_exists(\App\Http\Resources\Model\EstablishmentAccountResource::class ?? null, 'make')
                ? \App\Http\Resources\Model\EstablishmentAccountResource::make($est)
                : $est;
            return ['token' => $plain, 'guard' => 'establishment', 'user' => $userPayload];
        }

        // 2) Try User
        $user = null;
        if ($social) {
            $user = User::where('social_id', $social)->first();
        } elseif ($email) {
            $user = User::where('email', $email)->first();
        } elseif ($pp && $pn) {
            $user = User::where('prefix_phone', $pp)->where('phone_number', $pn)->first();
        }

        if ($user && $checkModel($user)) {
            $user->loadMissing(['directorate', 'directorate.ministry']);
            $plain = $user->createToken($device)->plainTextToken;
            $userPayload = UserResource::make($user);

            return ['token' => $plain, 'guard' => 'user', 'user' => $userPayload];
        }

        // فشل كل المحاولات
        throw new HttpResponseException(response()->json([
            'data' => null,
            'status' => false,
            'error' => __('messages.invalid_credentials') ?? 'Invalid credentials'
        ], 422));
    }
}
