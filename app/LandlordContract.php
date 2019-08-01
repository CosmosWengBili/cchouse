<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class LandlordContract extends Pivot
{
    
    use SoftDeletes;

    /**
     * Get the building of this landlord contract.
     */
    public function building() {
        return $this->hasOne('App\Building', 'landlord_contract_id');
    }

    /**
     * Get the landlord of this contract.
     */
    public function landlord() {
        return $this->belongsTo('App\Landlord');
    }
    
    /**
     * Get all contract files.
     */
    public function contractFiles()
    {
        return $this->morphMany('App\Document', 'attachable');
    }
    
    /**
     * Get the commissioner of this contract.
     */
    public function commissioner() {
        return $this->belongsTo('App\User', 'commissioner_id');
    }
    
}