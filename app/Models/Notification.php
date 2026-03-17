<?php

namespace App\Models;

use App\Models\BaseModel;

use Illuminate\Database\Eloquent\Model;

class Notification extends BaseModel
{protected $fillable = [
        'title' => 'title',
        'body' => 'body',
        'kind' => 'kind',
        'sender_type' => 'sender_type',
        'sender_id' => 'sender_id',
        'sent_at' => 'sent_at',
    ];

protected $casts = [
        'sender_id' => 'integer',
        'sent_at' => 'datetime',
    ];


    public function targets()
    {
        return $this->hasMany(NotificationTarget::class);
    }

    public function users()
    {
        return $this->hasMany(NotificationUser::class);
    }

    public function sender()
    {
        return $this->morphTo();
    }


    //
}
