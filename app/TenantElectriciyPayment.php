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

    protected $guarded = [];

    protected $hidden = ['pivot', 'deleted_at'];

    protected $casts = [
        'is_charge_off_done' => 'boolean',
        'due_time' => 'date',
        'ammeter_read_date' => 'date',
    ];

    /**
     * Get the tenant contract of this electricity payment.
     */
    public function tenantContract()
    {
        return $this->belongsTo('App\TenantContract', 'tenant_contract_id');
    }

    /**
     * Get the pay log of this tenant payment.
     */
    public function payLogs()
    {
        return $this->morphMany('App\PayLog', 'loggable');

    }

    /**
     * Get the receipts of this electricity payment.
     */
    public function receipts()
    {
        return $this->morphMany('App\Receipt', 'receiptable');
    }
}
