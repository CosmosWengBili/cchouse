<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class DebtCollection extends Model implements AuditableContract
{
    use SoftDeletes;
    use AuditableTrait;

    /**
     * Get the user who made this debt collection.
     */
    public function collector() {
        return $this->belongsTo('App\User', 'collector_id');
    }

    /**
     * Get the tenant contract of this debt collection.
     */
    public function tenantContract() {
        return $this->belongsTo('App\TenantContract');
    }
}
