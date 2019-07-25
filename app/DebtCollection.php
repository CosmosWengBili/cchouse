<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DebtCollection extends Model
{

    use SoftDeletes;

    /**
     * Get the user who made this debt collection.
     */
    public function collector() {
        return $this->belongsTo('App\User', 'colloector_id');
    }

    /**
     * Get the tenant contract of this debt collection.
     */
    public function tenantContract() {
        return $this->belongsTo('App\TenantContract');
    }
}
