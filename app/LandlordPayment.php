<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LandlordPayment extends Model
{

    use SoftDeletes;
    
    /**
     * Get the building of this landlord payment.
     */
    public function building() {
        return $this->belongsTo('App\Building');
    }
}
