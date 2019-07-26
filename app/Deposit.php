<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deposit extends Model
{
    
    use SoftDeletes;

    /**
     * Get the tenant contract of deposit.
     */
    public function tenantContract() {
        return $this->belongsTo('App\TenantContract');
    }
}
