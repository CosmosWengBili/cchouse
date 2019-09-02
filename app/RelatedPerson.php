<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RelatedPerson extends Model
{
    use SoftDeletes;

    protected $visible = [
        'id',
        'name',
        'phone',
        'relationship',
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'related_person_type',
        'related_person_id',
        'type',
        'name',
        'phone',
        'relationship'
    ];
}
