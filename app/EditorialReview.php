<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EditorialReview extends Model
{
    protected $guarded = [];

    protected $casts = [
        'edit_value' => 'array',
        'original_value' => 'array',
    ];
}
