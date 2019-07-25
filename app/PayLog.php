<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PayLog extends Model
{
    
    use SoftDeletes;

    /**
     * Get the owning loggable model.
     */
    public function loggable() {
        return $this->morphTo();
    }
}
