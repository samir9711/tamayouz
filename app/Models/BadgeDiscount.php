<?php

namespace App\Models;

use App\Models\BaseModel;

use Illuminate\Database\Eloquent\Model;

class BadgeDiscount extends BaseModel
{
    protected $fillable = [
        'badge_id' => 'badge_id',
        'establishment_id' => 'establishment_id',
        'title' => 'title',
        'description' => 'description',
        'note' => 'note',
        'discount_percent' => 'discount_percent',
        'valid_from' => 'valid_from',
        'valid_until' => 'valid_until',
        'status' => 'status',
        'categories' => 'categories',
        'scanned_at' => 'scanned_at',
    ];

    protected $casts = [
        'badge_id' => 'integer',
        'establishment_id' => 'integer',
        'discount_percent' => 'float',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'categories' => 'array',
        'scanned_at' => 'datetime',
    ];
    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }

    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
    }

    //
}
