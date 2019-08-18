<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

use Carbon\Carbon;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;
use phpDocumentor\Reflection\Types\Integer;

class TenantContract extends Pivot implements AuditableContract
{
    use SoftDeletes;
    use AuditableTrait;

    protected $hidden = ['deleted_at'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'set_other_rights' => 'boolean',
        'sealed_registered' => 'boolean',
        'effective' => 'boolean'
    ];

    protected $attributes = [
        'id',
    ];


    protected $appends = array('currentBalance');

    public function getCurrentBalanceAttribute() {
        return $this->calculateCurrentBalance();
    }

    /**
     * Get the tenant of this contract.
     */
    public function tenant()
    {
        return $this->belongsTo('App\Tenant');
    }

    /**
     * Get the room of this contract.
     */
    public function room()
    {
        return $this->belongsTo('App\Room');
    }

    public function building() {
        return $this->hasOneThrough(
            'App\Building',
            'App\Room',
            'id',
            'id',
            'room_id',
            'building_id'
        );
    }

    /**
     * Get the user who is the commissioner of this tenant contract.
     */
    public function commissioner()
    {
        return $this->belongsTo('App\User', 'commissioner_id');
    }

    /**
     * Get all the deposits of this tenant contract.
     */
    public function deposits()
    {
        return $this->hasMany('App\Deposit', 'tenant_contract_id');
    }

    /**
     * Get all the debt collections of this tenant contract.
     */
    public function debtCollections()
    {
        return $this->hasMany('App\DebtCollection', 'tenant_contract_id');
    }

    /**
     * Get all the maintenances of this tenant contract.
     */
    public function maintenances()
    {
        return $this->hasMany('App\Maintenance', 'tenant_contract_id');
    }

    /**
     * Get all the company incomes that this tenant contract ever made.
     */
    public function companyIncomes()
    {
        return $this->hasMany('App\CompanyIncome', 'tenant_contract_id');
    }

    /**
     * Get all the tenant payments of this tenant contract.
     */
    public function tenantPayments()
    {
        return $this->hasMany('App\TenantPayment', 'tenant_contract_id');
    }

    /**
     * Get all the pay logs of this tenant contract.
     */
    public function payLogs()
    {
        return $this->hasMany('App\PayLog', 'tenant_contract_id');
    }

    /**
     * Get all the tenant electricity payments of this tenant contract.
     */
    public function tenantElectricityPayments()
    {
        return $this->hasMany(
            'App\TenantElectricityPayment',
            'tenant_contract_id'
        );
    }

    /**
     * Get all the carrier documents of the tenant contract.
     * carrier_file
     * 載具檔案
     */
    public function carrierFiles()
    {
        return $this->documents()->where('document_type', 'carrier_file');
    }

    /**
     * Get all of the landlords's documents.
     */
    public function documents()
    {
        return $this->morphMany('App\Document', 'attachable');
    }

    /**
     * Get landlords's thirdPartyDocuments.
     * 原檔
     */
    public function originalFiles()
    {
        return $this->documents()->where('document_type', 'original_file');
    }

    public function calculateCurrentBalance() {

        $unpaid = $this->tenantPayments()->sum('amount');
        $electricityUnpaid = $this->tenantElectricityPayments()->sum('amount');
        $paid = $this->payLogs()->sum('amount');

        return $paid - $unpaid - $electricityUnpaid;
    }

    /**
     * Scope a query to only include active tenant contracts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query
            ->where('contract_end', '>=', Carbon::today())
            ->where('contract_start', '<=', Carbon::today());
    }
}
