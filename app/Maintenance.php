<?php

namespace App;

use App\Traits\WithExtraInfo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Maintenance extends Model implements AuditableContract
{
    use SoftDeletes;
    use AuditableTrait;
    use WithExtraInfo;

    const STATUSES = [
        'pending' => '待處理',
        'contact' => '聯繫中',
        'sent' => '已派工',
        'request' => '請款中',
        'done' => '案件完成',
        'cancel' => '已取消',
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
        'clean' => '清潔',
        'others' => '其它'
    ];

    protected $guarded = [];

    protected $hidden = ['pivot', 'deleted_at'];
    /**
     * Get the user who took care of this maintenance.
     */
    public function commissioner()
    {
        return $this->belongsTo('App\User', 'commissioner_id');
    }

    /**
     * Get the user who went for the maintenance service.
     */
    public function maintenanceStaff()
    {
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
     * Get the tenant of this maintenance.
     */
    public function tenant()
    {
        return $this->hasOneThrough(
            'App\Tenant',
            'App\TenantContract',
            'id',
            'id',
            'tenant_contract_id',
            'tenant_id'
        );
    }

    /**
     * Get the tenant of this maintenance.
     */
    public function room()
    {
        return $this->hasOneThrough(
            'App\Room',
            'App\TenantContract',
            'id',
            'id',
            'tenant_contract_id',
            'room_id'
        );
    }

    /**
     * Get all of the maintenance's documents.
     */
    public function documents()
    {
        return $this->morphMany('App\Document', 'attachable');
    }
    /**
     * Get all of the maintenance's pictures.
     * 照片
     */
    public function pictures()
    {
        return $this->documents()->where('document_type', 'picture');
    }

    /**
     * Get income amount.
     * 取得收入金額
     */
    public function incomeAmount()
    {
        $cost = $this->cost;
        $price = $this->price;

        return $price - $cost;
    }

    /**
     * Get the receipts of this maintenance.
     */
    public function receipts()
    {
        return $this->morphMany('App\Receipt', 'receiptable');
    }
    
    public function companyIncomes()
    {
        return $this->morphMany('App\CompanyIncome', 'incomable');
    }
}
