<?php

namespace App;

use App\Traits\WithExtraInfo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class DebtCollection extends Model implements AuditableContract
{
    use \Znck\Eloquent\Traits\BelongsToThrough;
    use SoftDeletes;
    use AuditableTrait;
    use WithExtraInfo;

    protected $guarded = [];
    protected $hidden = ['pivot', 'deleted_at'];
    protected $casts = [
        'is_penalty_collected' => 'boolean'
    ];

    /**
     * Get the user who made this debt collection.
     */
    public function collector()
    {
        return $this->belongsTo('App\User', 'collector_id');
    }

    /**
     * Get the tenant contract of this debt collection.
     */
    public function tenantContract()
    {
        return $this->belongsTo('App\TenantContract');
    }

    /**
     * Get room
     *
     * @return \Znck\Eloquent\Relations\BelongsToThrough
     */
    public function room()
    {
        return $this->belongsToThrough('App\Room', 'App\TenantContract');
    }

    /**
     * Get pay logs
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function payLogs()
    {
        return $this->hasManyThrough(
            'App\PayLog',
            'App\TenantContract',
            'id',
            'tenant_contract_id',
            'tenant_contract_id'
        );

    }

    /**
     * Get all the tenant payments of this debt collection.
     */
    public function tenantPayments()
    {
        return $this->hasMany(
            'App\TenantPayment',
            'tenant_contract_id',
            'tenant_contract_id'
        );
    }

    /**
     * Get all the tenant electricity payments of this debt collection.
     */
    public function tenantElectricityPayments()
    {
        return $this->hasMany(
            'App\TenantElectricityPayment',
            'tenant_contract_id',
            'tenant_contract_id'
        );
    }

    /**
     * Get the receipts of this debt collection.
     */
    public function receipts()
    {
        return $this->morphToMany('App\Receipt', 'receiptable');
    }

}
