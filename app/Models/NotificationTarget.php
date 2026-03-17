<?php

namespace App\Models;

use App\Models\BaseModel;

use Illuminate\Database\Eloquent\Model;

class NotificationTarget extends BaseModel
{
    protected $fillable = [
        'notification_id' => 'notification_id',
        'target_type' => 'target_type',
        'target_id' => 'target_id',
    ];

    protected $casts = [
            'notification_id' => 'integer',
            'target_id' => 'integer',
        ];
    public function notification()
    {
        return $this->belongsTo(Notification::class);
    }

    //
}
