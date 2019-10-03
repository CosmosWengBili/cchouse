<?php

namespace App;

use App\Services\SmsService;
use App\Traits\WithExtraInfo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;
use phpDocumentor\Reflection\Types\Integer;

class TenantContract extends Pivot implements AuditableContract
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

    protected $hidden = ['pivot', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'set_other_rights' => 'boolean',
        'sealed_registered' => 'boolean',
        'effective' => 'boolean',
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

    /**
     * Get all the payOff of this tenant contract.
     */
    public function payOff()
    {
        return $this->hasOne('App\PayOff', 'tenant_contract_id');
    }

    public function calculateCurrentBalance() {

        $month = 0;
        $payment_date = '';

        if( Carbon::now()->format('d') < $this->rent_pay_day ){
            $month = Carbon::now()->subMonth()->month;
            $payment_date = Carbon::create(Carbon::now()->year,$month,$this->rent_pay_day);
            $unpaid = $this->tenantPayments()->where('due_time', '<=', $payment_date)->sum('amount');
            $electricityUnpaid = $this->tenantElectricityPayments()->where('due_time', '<', $payment_date)->sum('amount');
        }
        else{
            $month = Carbon::now()->month;
            $payment_date = Carbon::create(Carbon::now()->year, $month, $this->rent_pay_day);
            $unpaid = $this->tenantPayments()->where('due_time', '<=', $payment_date)->sum('amount');
            $electricityUnpaid = $this->tenantElectricityPayments()->where('due_time', '<=', $payment_date)->sum('amount');
        }

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

    /**
     * Get the receipts of this tenant contracts.
     */
    public function receipts()
    {
        return $this->morphToMany('App\Receipt', 'receiptable');
    }

    public function sendElectricityPaymentReportSMS(int $year, int $month) {
        $smsService = resolve(SmsService::class);
        $mobile = $this->tenant()->first()->phones()->first()->value;
        $url = route('tenantContracts.electricityPaymentReport', [
            'tenantContract' => $this->id,
            'year' => $year,
            'month' => $month
        ]);
        $shouldPay = $this->electricityPaymentAmount($year, $month);
        $smsService->send($mobile, "本期總應繳電費為: $shouldPay, 電費明細請參考: {$url}");
    }

    /**
     * 取得下一期的 TenantContract
     */
    public function nextTenantContract() {
        return $this->tenant
             ->tenantContracts()
             ->where('tenant_contract.id', '>', $this->id)
             ->where('tenant_contract.contract_start', '>=', $this->contract_start)
             ->orderBy('tenant_contract.contract_start', 'asc')
             ->first();
    }

    private function electricityPaymentAmount($year, $month) {
        $data = $this->room()->first()->buildElectricityPaymentData($year, $month);
        return $data['本期應付金額'];
    }
}
