<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Landlord extends Model
{

    use SoftDeletes;
    
    /**
     * Get all the landlord's contracts.
     */
    public function landlordContracts() {
        return $this->hasMany('App\LandlordContract');
    }

    /**
     * Get all the buildings through the contracts that this landlord owns.
     */
    public function buildings() {
        return $this->hasManyThrough('App\Building', 'App\LandlordContract', 'landlord_id', 'landlord_contract_id');
    }

    /**
     * Get all of the landlords's documents.
     * 代收文件
     */
    public function thirdPartyDocuments()
    {
        return $this->morphMany('App\Document', 'attachable');
    }
    
}
