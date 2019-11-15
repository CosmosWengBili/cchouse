<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class PayOff extends Model implements AuditableContract
{
    use SoftDeletes;
    use AuditableTrait;

    protected $casts = [
        'payment_detail' => 'array',
    ];

    protected $guarded = [];

    protected $hidden = ['pivot', 'deleted_at'];

    /**
     * Get the tenant contract of this tenant payment.
     */
    public function tenantContract()
    {
        return $this->belongsTo('App\TenantContract', 'tenant_contract_id');
    }

    public function details()
    {
        return $this->hasMany('App\PayOffDetail');
    }
}
