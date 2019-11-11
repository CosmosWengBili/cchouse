<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class PayLog extends Model implements AuditableContract
{
    use SoftDeletes;
    use AuditableTrait;

    protected $casts = [
        'paid_at' => 'datetime',
    ];
    protected $guarded = [];

    protected $hidden = ['pivot', 'deleted_at'];

    /**
     * Get the owning loggable model.
     */
    public function loggable()
    {
        return $this->morphTo();
    }

    public function tenantContract()
    {
        return $this->belongsTo('App\TenantContract', 'tenant_contract_id');
    }
    /**
     * Get the receipts of this deposit.
     */
    public function receipts()
    {
        return $this->morphMany('App\Receipt', 'receiptable');
    }

    public function getCommissionType() {
        try {
            return $this->tenantContract
                        ->room
                        ->building
                        ->activeContracts()
                        ->first()
                        ->commission_type;
        } catch (\Exception $e) {}

        return null;
    }

    public function getRoomId() {
        try {
             if ($this->tenantContract) {
                 return $this->tenantContract->room_id;
             }

             return $this->loggable->tenantContract->room_id;
        } catch (\Exception $e) {}

        return null;
    }
}
