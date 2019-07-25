<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{

    use SoftDeletes;

    /**
     * Get the building that this room is in.
     */
    public function building() {
        return $this->belongsTo('App\Building');
    }

    /**
     * Get all the tenant contracts of this room.
     */
    public function tenantContracts() {
        return $this->hasMany('App\TenantContract');
    }

    /**
     * Get all the tenant contracts that is currently active of this room.
     */
    public function activeContracts() {
        return $this->hasMany('App\TenantContract')->active();
    }

    /**
     * Get all the tenants who ever lived in this room.
     */
    public function tenantsHistory() {
        return $this->belongsToMany('App\Tenant', 'App\TenantContract');
    }

    /**
     * Get all keys of this room.
     */
    public function keys() {
        return $this->hasMany('App\Key');
    }

    /**
     * Get all appliances of this room.
     */
    public function appliances() {
        return $this->hasMany('App\Appliance');
    }

    /**
     * Get all of the room's pictures.
     * 照片
     */
    public function pictures() {
        return $this->morphMany('App\Document', 'attachable');
    }
}
