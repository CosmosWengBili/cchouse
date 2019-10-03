<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Receipt extends Model implements AuditableContract
{
    use SoftDeletes;
    use AuditableTrait;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    
    protected $hidden = ['pivot', 'deleted_at'];

    /**
     * Get the tenant contracts.
     */
    public function tenantContracts()
    {
        return $this->morphedByMany('App\TenantContract', 'receiptable');
    }

    /**
     * Get the tenant payments.
     */
    public function tenantPayments()
    {
        return $this->morphedByMany('App\TenantPayment', 'receiptable');
    }

    /**
     * Get the tenant electricity payments.
     */
    public function tenantElectricityPayments()
    {
        return $this->morphedByMany('App\TenantElectricityPayment', 'receiptable');
    }

    /**
     * Get the tenant landlord payments.
     */
    public function landlordPayments()
    {
        return $this->morphedByMany('App\LandlordPayment', 'receiptable');
    }

    /**
     * Get the tenant deposits.
     */
    public function deposits()
    {
        return $this->morphedByMany('App\Deposit', 'receiptable');
    }

    /**
     * Get the tenant debt collections.
     */
    public function debtCollections()
    {
        return $this->morphedByMany('App\DebtCollection', 'receiptable');
    }
}
