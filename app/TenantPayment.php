<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TenantPayment extends Model
{

    use SoftDeletes;

    /**
     * Get the tenant contract of this tenant payment.
     */
    public function tenantContract() {
        return $this->belongsTo('App\TenantContract', 'tenant_contract_id');
    }

    /**
     * Get the pay log of this tenant payment.
     */
    public function payLog() {
        return $this->morphOne('App\PayLog', 'loggable');
    }
}
