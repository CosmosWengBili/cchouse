<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EditorialReview extends Model
{
    protected $guarded = [];

    protected $casts = [
        'edit_value' => 'array',
        'original_value' => 'array',
        'diff' => 'array',
        'extra_data' => 'array',
    ];

    protected $appends = ['diffs'];

    protected $hidden = ['deleted_at'];

    public function getDiffsAttribute()
    {
        return array_diff(
            $this->edit_value,
            $this->original_value
        );
    }
}
