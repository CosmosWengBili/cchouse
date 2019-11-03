<?php

namespace App;

use App\Traits\WithExtraInfo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Deposit extends Model implements AuditableContract
{
    use SoftDeletes;
    use AuditableTrait;
    use WithExtraInfo;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $hidden = ['pivot', 'deleted_at', 'reason_of_deletions'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_deposit_collected' => 'boolean',
        'confiscated_or_returned_date' => 'datetime:Y-m-d',
        'payer_is_legal_person' => 'boolean',
        'appointment_date' => 'date',
    ];

    /**
     * Get the tenant contract of deposit.
     */
    public function tenantContract()
    {
        return $this->belongsTo('App\TenantContract');
    }

    /**
     * Get the room of deposit.
     */
    public function room()
    {
        return $this->belongsTo('App\Room');
    }

    /**
     * Get the receipts of this deposit.
     */
    public function receipts()
    {
        return $this->morphToMany('App\Receipt', 'receiptable');
    }

    // 是否為代管
    public function isManagedByCompany() {
        $contract = $this->tenantContract;
        if (!$contract) return false;

        $building = $contract->building;
        if (!$building) return false;

        $landlordContract = $building->activeContracts()->first();
        if (!$landlordContract) return false;

        return $landlordContract->commission_type == '代管';
    }
}
