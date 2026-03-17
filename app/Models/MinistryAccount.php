<?php

namespace App\Models;

use App\Models\BaseModel;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Hash;

class MinistryAccount extends BaseAuthModel
{
    use HasRoles;
    protected $fillable = [
        'ministry_id' => 'ministry_id',
        'name' => 'name',
        'email' => 'email',
        'password' => 'password',
        'role' => 'role',
    ];

protected $casts = [
        'ministry_id' => 'integer',
    ];

     protected $hidden = [
        'password',
        'remember_token',
    ];

    //
    public function ministry()
    {
        return $this->belongsTo(Ministry::class);
    }

    public function setPasswordAttribute(?string $value): void
    {
        if ($value === null || $value === '') {
            $this->attributes['password'] = $value;
            return;
        }

        $this->attributes['password'] = Hash::needsRehash($value) ? Hash::make($value) : $value;
    }
}
