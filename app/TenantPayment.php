<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class TenantPayment extends Model implements AuditableContract
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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_charge_off_done' => 'boolean',
        'is_visible_at_report' => 'boolean',
        'is_pay_off' => 'boolean',
        'due_time' => 'datetime:Y-m-d'
    ];
    /**
     * Get the tenant contract of this tenant payment.
     */
    public function tenantContract()
    {
        return $this->belongsTo('App\TenantContract', 'tenant_contract_id');
    }

    /**
     * Get the pay log of this tenant payment.
     */
    public function payLogs()
    {
        return $this->morphMany('App\PayLog', 'loggable');
    }

    /**
     * Get the receipts of this tenant payment.
     */
    public function receipts()
    {
        return $this->morphMany('App\Receipt', 'receiptable');
    }

    /**
     * 是否為欠繳
     */
    public function isUnderpaid()
    {
        if (is_null($this->due_time)) {
            return false;
        }
        $now = Carbon::now();
        return !$this->is_charge_off_done && $now->isAfter($this->due_time);
    }
}
