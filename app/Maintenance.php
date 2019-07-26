<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class Maintenance extends Pivot
{
    
    use SoftDeletes;

    /**
     * Get the user who took care of this maintenance.
     */
    public function commissioner() {
        return $this->belongsTo('App\User', 'commissioner_id');
    }

    /**
     * Get the user who went for the maintenance service.
     */
    public function maintenanceStaff() {
        return $this->belongsTo('App\User', 'maintenance_staff_id');
    }

    /**
     * Get the tenant contract of this maintenance.
     */
    public function tenantContract()
    {
        return $this->belongsTo('App\TenantContract', 'tenant_contract_id');
    }

    /**
     * Get all of the related pictures.
     * 相關照片
     */
    public function pictures() {
        return $this->morphMany('App\Document', 'attachable');
    }
}
