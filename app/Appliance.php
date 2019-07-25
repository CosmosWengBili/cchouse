<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appliance extends Model
{

    use SoftDeletes;

    /**
     * Get the room this appliance belongs to.
     */
    public function room() {
        return $this->belongsTo('App\Room');
    }
}
