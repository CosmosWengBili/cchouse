<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Maintenance extends Pivot implements AuditableContract
{
    use SoftDeletes;
    use AuditableTrait;

    const STATUSES = [
        'pending' => '待處理',
        'contact' => '聯繫中',
        'sent' => '已派工',
        'request' => '請款中',
        'done' => '案件完成',
    ];
    const INCIDENT_TYPES = [
        'clean' => '清潔',
        'repair' => '維修',
    ];
    const WORK_TYPES = [
        'water_and_electricity' => '水電',
        'paint' => '油漆',
        'wood' => '木工',
        'air_conditioning' => '冷氣',
        'leaking' => '漏水',
        'doors' => '門窗',
        'wallpaper' => '壁紙',
        'internet' => '網路',
        'appliance' => '家電',
        'others' => '其它',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get the user who took care of this maintenance.
     */
    public function commissioner() {
        return $this->belongsTo('App\User', 'commissioner_id');
    }

    /**
     * Get the user who went for the maintenance service.
     */
    public function maintenanceStaff() {
        return $this->belongsTo('App\User', 'maintenance_staff_id');
    }

    /**
     * Get the tenant contract of this maintenance.
     */
    public function tenantContract()
    {
        return $this->belongsTo('App\TenantContract', 'tenant_contract_id');
    }

    /**
     * Get all of the related pictures.
     * 相關照片
     */
    public function pictures() {
        return $this->morphMany('App\Document', 'attachable');
    }
}
