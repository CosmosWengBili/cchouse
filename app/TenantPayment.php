<?php

namespace App;

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

    protected $hidden = ['pivot'];

    protected $fillable = [
        "subject",
        "tenant_contract_id",
        "due_time",
        "amount",
        "is_charge_off_done",
        "charge_off_date",
        "invoice_serial_number",
        "collected_by",
        "is_visible_at_report",
        "is_pay_off",
        "comment",
    ];

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
        return $this->morphToMany('App\Receipt', 'receiptable');
    }
}
