<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{

    use SoftDeletes;

    /**
     * Get the owning attachable model.
     */
    public function attachable() {
        return $this->morphTo();
    }
}
