<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Room extends Model implements AuditableContract
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
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'needs_decoration' => 'boolean',
        'has_digital_tv' => 'boolean',
    ];

    /**
     * Get the building that this room is in.
     */
    public function building()
    {
        return $this->belongsTo('App\Building');
    }

    /**
     * Get all the tenant contracts of this room.
     */
    public function tenantContracts()
    {
        return $this->hasMany('App\TenantContract');
    }

    /**
     * Get all the tenant contracts that is currently active of this room.
     */
    public function activeContracts()
    {
        return $this->hasMany('App\TenantContract')->active();
    }

    /**
     * Get all the tenants who ever lived in this room.
     */
    public function tenantsHistory()
    {
        return $this->belongsToMany('App\Tenant', 'App\TenantContract');
    }

    /**
     * Get all keys of this room.
     */
    public function keys()
    {
        return $this->hasMany('App\Key');
    }

    /**
     * Get all appliances of this room.
     */
    public function appliances()
    {
        return $this->hasMany('App\Appliance');
    }

    /**
     * Get all of the room's documents.
     * 照片
     */
    public function documents()
    {
        return $this->morphMany('App\Document', 'attachable');
    }
    /**
     * Get all of the room's pictures.
     * 照片
     */
    public function pictures()
    {
        return $this->documents()->where('document_type', 'picture');
    }

    /**
     * Get the landlord payments of this room.
     */
    public function landlordPayments()
    {
        return $this->hasMany('App\LandlordPayment');
    }

    /**
     * Get all landlord other subjects of this building.
     */
    public function landlordOtherSubjects()
    {
        return $this->hasMany('App\LandlordOtherSubject');
    }

    public function tenantElectricityPayments() {
        return $this->hasManyThrough(
            'App\TenantElectricityPayment',
            'App\TenantContract',
            'room_id',
            'tenant_contract_id',
            'id',
            'id'
        );
    }

    public function buildElectricityPaymentData(int $year, int $month) {
        $thisMonth = new Carbon("$year-$month-1");
        $lastMonth = $thisMonth->copy()->subMonth(1);
        $last2Month = $thisMonth->copy()->subMonth(2);
        $last3Month = $thisMonth->copy()->subMonth(3);
        $result = [
            "{$last3Month->month}月應付" => null,
            "{$last3Month->month}月已付" => null,
            "{$last2Month->month}月應付" => null,
            "{$last2Month->month}月已付" => null,
            "{$lastMonth->month}月應付" => null,
            "{$lastMonth->month}月已付" => null,
            "上期 110v 起" => null,
            "上期 220v 起" => null,
            "本期 110v 結" => null,
            "本期 220v 結" => null,
            "元 / 度" => null,
            "用電金額" => null,
            "前期電費欠額" => null,
            "本期應付金額" => null,
            "房號" => $this->room_number,
        ];

        $tenantElectricityPayments = $this->tenantElectricityPayments()
            ->whereBetween('due_time', [
                $last3Month->startOfMonth(),
                $thisMonth->endOfMonth()
            ])->get();

        foreach ($tenantElectricityPayments as $tenantElectricityPayment) {
            $month = $tenantElectricityPayment->due_time->month;

            # 本期
            if ($month == $thisMonth->month) {
                $result['上期 110v 起'] = $tenantElectricityPayment['110v_start_degree'];
                $result['上期 220v 起'] = $tenantElectricityPayment['220v_start_degree'];
                $result['本期 110v 結'] = $tenantElectricityPayment['110v_end_degree'];;
                $result['本期 220v 結'] = $tenantElectricityPayment['220v_end_degree'];
                if (in_array($month, [7, 8, 9, 10])) {
                    $result['元 / 度'] = $tenantElectricityPayment->tenantContract()->first()['electricity_price_per_degree_summer'];
                } else {
                    $result['元 / 度'] = $tenantElectricityPayment->tenantContract()->first()['electricity_price_per_degree'];
                }
                $result['用電金額'] = $tenantElectricityPayment->amount;
            } else {
                $result["{$month}月應付"] = $tenantElectricityPayment->amount;
                $result["{$month}月已付"] = $tenantElectricityPayment->payLogs()->sum('amount');
            }
        }
        $result['前期電費欠額'] = ($result["{$lastMonth->month}月應付"] ?? 0) - ($result["{$lastMonth->month}月已付"] ?? 0);
        $result['本期應付金額'] = ($result['用電金額'] ?? 0) + $result['前期電費欠額'] ?? 0;

        return $result;
    }
}
