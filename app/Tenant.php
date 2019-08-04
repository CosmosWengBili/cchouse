<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Tenant extends Model implements AuditableContract
{
    use SoftDeletes;
    use AuditableTrait;

    /**
     * Get all the contracts of this tenant.
     */
    public function tenantContracts() {
        return $this->hasMany('App\TenantContract');
    }

    /**
     * Get all the currently active contracts of this tenant.
     */
    public function activeContracts() {
        return $this->hasMany('App\TenantContract')->active();
    }

    /**
     * Get all the rooms that this tenant ever lived in.
     */
    public function roomsHistory() {
        return $this->belongsToMany('App\Room', 'App\TenantContract');
    }

    /**
     * Get the user who confirmed the tenant's information.
     */
    public function confirmBy() {
        return $this->belongsTo('App\User', 'confirm_by');
    }
}
