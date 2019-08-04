<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LandlordOtherSubject extends Model
{
    
    use SoftDeletes;

    /**
     * Get the building of this landlord other subject.
     */
    public function building() {
        return $this->belongsTo('App\Room');
    }
}
