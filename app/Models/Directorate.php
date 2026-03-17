<?php

namespace App\Models;

use App\Models\BaseModel;

use Illuminate\Database\Eloquent\Model;

class Directorate extends BaseModel
{
    protected $fillable = [
        'ministry_id' => 'ministry_id',
        'name' => 'name',
        'description' => 'description',
        'contact_phone' => 'contact_phone',
        'contact_email' => 'contact_email',
    ];

    protected $casts = [
        'ministry_id' => 'integer',
    ];


     protected $search = ['name', 'description'];


    protected array $filterable = [
        'ministry_id'=>'int'
    ];

    protected array $dynamicFilterColumns = [
        'ministry_id' ,
    ];





    public function ministry()
    {
        return $this->belongsTo(Ministry::class);
    }

    //
}
