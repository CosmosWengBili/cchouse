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

    public $incrementing = true;
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
        'set_other_rights'  => 'boolean',
        'sealed_registered' => 'boolean',
        'contract_end'      => 'date',
        'contract_start'    => 'date',
    ];

    protected $appends = ['currentBalance'];

    public function getCurrentBalanceAttribute()
    {
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

    public function building()
    {
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
        return $this->morphMany('App\CompanyIncome', 'incomable');
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
        )->select([
            'tenant_electricity_payments.id', # 編號
            'tenant_electricity_payments.tenant_contract_id', # 關聯資料編號
            'tenant_electricity_payments.subject', # 科目
            'tenant_electricity_payments.due_time', # 應繳時間
            'tenant_electricity_payments.is_charge_off_done', # 是否沖銷
            'tenant_electricity_payments.charge_off_date', # 沖銷日期
            'tenant_electricity_payments.amount', # 費用,
            'tenant_electricity_payments.*'
        ]);
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

    public function calculateCurrentBalance()
    {
        $month        = 0;
        $payment_date = '';

        if (Carbon::now()->format('d') < $this->rent_pay_day) {
            $month             = Carbon::now()->subMonth()->month;
            $payment_date      = Carbon::create(Carbon::now()->year, $month, $this->rent_pay_day);
            $unpaid            = $this->tenantPayments()->where('due_time', '<=', $payment_date)->sum('amount');
            $electricityUnpaid = $this->tenantElectricityPayments()->where('due_time', '<', $payment_date)->sum('amount');
        } else {
            $month             = Carbon::now()->month;
            $payment_date      = Carbon::create(Carbon::now()->year, $month, $this->rent_pay_day);
            $unpaid            = $this->tenantPayments()->where('due_time', '<=', $payment_date)->sum('amount');
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

    public function sendElectricityPaymentReportSMS(int $year, int $month)
    {
        $smsService = resolve(SmsService::class);
        $mobile     = $this->tenant()->first()->phones()->first()->value;
        $createdAt  = Carbon::now()->getTimestamp();
        $url        = route('tenantContracts.electricityPaymentReport', [
            'data' => base64_encode("{$this->id}|{$year}|${month}|{$createdAt}")
        ]);

        $shouldPay = $this->electricityPaymentAmount($year, $month);
        $smsService->send($mobile, "兆基物業管理提醒您，本期總應繳電費為: $shouldPay, 電費明細請參考: {$url}");
    }

    /**
     * 取得下一期的 TenantContract
     */
    public function nextTenantContract()
    {
        return $this->tenant
             ->tenantContracts()
             ->where('tenant_contract.id', '>', $this->id)
             ->where('tenant_contract.contract_start', '>=', $this->contract_start)
             ->orderBy('tenant_contract.contract_start', 'asc')
             ->first();
    }

    /*
     * 如果該 Room 的 landlord Contract 為包租，
     * 然後 landlord 和 tenant 都不是法人 is_legal_person，
     * 則該 receipt_type 為收據
     */
    public function getReceiptType()
    {
        try {
            $room = $this->room;
            $building = $room->building;
            $landlordContract = $building->activeContracts()->first();
            $tenantContract = $room->activeContracts()->first();
            $tenant = $tenantContract->tenant;
            $tenantIsLegalPerson = $tenant->is_legal_person;
            $landlordIsLegalPerson = $landlordContract->landlords()->where('is_legal_person', true)->exists();

            if ($landlordContract->commission_type == '包租' && !$tenantIsLegalPerson && !$landlordIsLegalPerson) {
                return '收據';
            }
        } catch (\Exception $e) {
        }

        return '發票';
    }

    private function electricityPaymentAmount($year, $month)
    {
        $data = $this->room()->first()->buildElectricityPaymentData($year, $month);

        return $data['本期應付金額'];
    }
}
