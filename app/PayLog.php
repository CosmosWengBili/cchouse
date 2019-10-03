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
}
