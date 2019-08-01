<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Building extends Model
{
    
    use SoftDeletes;

    /**
     * Get the user who is the commissioner of this building.
     */
    public function commissioner() {
        return $this->belongsTo('App\User', 'commissioner_id');
    }

    /**
     * Get the user who is the administrator of this building.
     */
    public function administrator() {
        return $this->belongsTo('App\User', 'administrator_id');
    }

    /**
     * Get the rooms of this building.
     */
    public function rooms() {
        return $this->hasMany('App\Room');
    }

    /**
     * Get the landlord contract of this building.
     */
    public function landlordContract() {
        return $this->belongsTo('App\LandlordContract', 'landlord_contract_id');
    }

    /**
     * Get the landlord payments of this building.
     */
    public function landlordPayments() {
        return $this->hasMany('App\LandlordPayment');
    }

    /**
     * Get all landlord other subjects of this building.
     */
    public function landlordOtherSubjects() {
        return $this->hasMany('App\LandlordOtherSubject');
    }

    /**
     * Get the shareholders of this building.
     */
    public function shareholders()
    {
        return $this->belongsToMany('App\Shareholder');
    }
}