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

    public function setRoomCodeAttribute($value)
    {
        $building = $this->building;
        $room_code = 'B'.$building->building_code;

        if ($this->room_layout == '公區') {
            $room_code .= 'P'.$this->room_number;
        } else {
            $room_code .= 'G'.$this->room_number;
        }

        $this->attributes['room_code'] = $room_code;
    }

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
     * Get all the tenant contracts that is currently active of this room.
     */
    public function deposits()
    {
        return $this->hasMany('App\Deposit');
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
     * Get all maintenances of this room.
     */
    public function maintenances()
    {
        return $this->hasMany('App\Maintenance');
    }

    public function roomMaintenances()
    {
        return $this->hasMany('App\RoomMaintenance');
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

    public function tenantElectricityPayments()
    {
        return $this->hasManyThrough(
            'App\TenantElectricityPayment',
            'App\TenantContract',
            'room_id',
            'tenant_contract_id',
            'id',
            'id'
        );
    }

    public function buildElectricityPaymentData(int $year, int $month)
    {
        $thisMonth = new Carbon("$year-$month-1");
        $range = [$thisMonth->copy()->startOfMonth(), $thisMonth->copy()->endOfMonth()];
        $result = [
            '上期 110v 起' => 'N/A',
            '上期 220v 起' => 'N/A',
            '本期 110v 結' => 'N/A',
            '本期 220v 結' => 'N/A',
            '元 / 度' => 'N/A',
            '用電金額' => 'N/A',
            '前期電費欠額' => 'N/A',
            '本期應付金額' => 'N/A',
            '房號' => $this->room_number,
        ];

        $tenantElectricityPayment = $this->tenantElectricityPayments()
                                         ->whereBetween('due_time', $range)
                                         ->first(); // 本期應繳電費
        if ($tenantElectricityPayment) {
            $result['上期 110v 起'] = $tenantElectricityPayment['110v_start_degree'];
            $result['上期 220v 起'] = $tenantElectricityPayment['220v_start_degree'];
            $result['本期 110v 結'] = $tenantElectricityPayment['110v_end_degree'];
            ;
            $result['本期 220v 結'] = $tenantElectricityPayment['220v_end_degree'];
            if (in_array($month, [7, 8, 9, 10])) {
                $result['元 / 度'] = $tenantElectricityPayment->tenantContract()->first()['electricity_price_per_degree_summer'];
            } else {
                $result['元 / 度'] = $tenantElectricityPayment->tenantContract()->first()['electricity_price_per_degree'];
            }
            $result['用電金額'] = $tenantElectricityPayment->amount;
        }
        // 本月以前的未繳電費總額
        $lackAmount = $this->tenantElectricityPayments()
                           ->where('due_time', '<', $range[0])
                           ->where('is_charge_off_done', false)
                           ->sum('amount');
        $result['前期電費欠額'] = $lackAmount;
        $result['本期應付金額'] = $result['前期電費欠額'] + ($result['用電金額'] != 'N/A' ? $result['用電金額'] : 0);

        return $result;
    }
}
