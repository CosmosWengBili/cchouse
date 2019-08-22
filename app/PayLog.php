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
        'paid_at' => 'datetime:Y-m-d',
    ];
    protected $fillable = [
        'loggable_type',
        'loggable_id',
        'subject',
        'payment_type',
        'amount',
        'virtual_account',
        'paid_at',
        'tenant_contract_id',
        'receipt_type'
    ];

    /**
     * Get the owning loggable model.
     */
    public function loggable()
    {
        return $this->morphTo();
    }
}
