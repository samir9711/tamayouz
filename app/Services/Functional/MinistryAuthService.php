<?php

namespace App\Services\Functional;

use App\Http\Requests\Basic\BasicRequest;
use App\Http\Resources\Model\MinistryResource;
use App\Http\Resources\Model\MinistryAccountResource;
use App\Models\Mnistry;
use App\Models\MinistryAccount;
use App\Services\Basic\BaseAuthService;
use App\Support\OtpChannelHelper;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MinistryAuthService extends BaseAuthService
{
    protected function setVariables(): void
    {
        $this->model    = MinistryAccount::class;
        $this->key      = 'ministry_account';
        $this->resource = MinistryAccountResource::class;
        $this->guard    = 'ministry';
    }

    public function login(BasicRequest $request): array
    {
        $email  = $request->input('email');
        $pass   = $request->input('password');

        $ministry = $this->model::where('email', $email)->first();
        if (!$ministry || !$ministry->password || !Hash::check($pass, $ministry->password)) {
            throw new HttpResponseException($this->requiredField(__('messages.invalid_credentials')));
        }


        $ministry->loadMissing(['ministry']);

        [$token] = $this->issueToken($ministry, $request->input('token_device'));
        return ['token' => $token, $this->key => $this->resource::make($ministry)];
    }


    public function verifyOtp(BasicRequest $request): array
    {
        if($request->purpose === "phone_change")
            return $this->verifyPhoneChangeOtp($request);

        $user = $this->model::where("email",$request->input('email'))->firstOrFail();
        $channel = $user->otp_delivery_method ?? "email";

        $ok = OtpChannelHelper::verify($user, $request->input("purpose"), (string) $request->input('otp'), $channel);
        if (!$ok) {
            throw new HttpResponseException($this->requiredField(__('messages.invalid_otp')));
        }

        return [
            'message' => "",
            $this->key => $this->resource::make($user->fresh()),
        ];
    }
}
