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
    protected $guarded = [];
    
    protected $hidden = ['pivot', 'deleted_at'];
}
