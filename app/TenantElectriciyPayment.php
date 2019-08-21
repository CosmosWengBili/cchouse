<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class TenantElectricityPayment extends Model implements AuditableContract
{
    use SoftDeletes;
    use AuditableTrait;

    /**
     * Get the tenant contract of this electricity payment.
     */
    public function tenantContract()
    {
        return $this->belongsTo('App\TenantContract', 'tenant_contract_id');
    }

    /**
     * Get the pay log of this electricity payment.
     */
    public function payLog()
    {
        return $this->morphMany('App\PayLog', 'loggable');
    }

    /**
     * Get the receipts of this electricity payment.
     */
    public function receipts()
    {
        return $this->morphToMany('App\Receipt', 'receiptable');
    }
}
