<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Deposit extends Model implements AuditableContract
{
    use SoftDeletes;
    use AuditableTrait;

    /**
     * Get the tenant contract of deposit.
     */
    public function tenantContract()
    {
        return $this->belongsTo('App\TenantContract');
    }
}
