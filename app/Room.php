<?php

namespace App;

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

    protected $hidden = ['pivot'];
    
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
        'can_keep_pets' => 'boolean',
    ];

    /**
     * Get the building that this room is in.
     */
    public function building() {
        return $this->belongsTo('App\Building');
    }

    /**
     * Get all the tenant contracts of this room.
     */
    public function tenantContracts() {
        return $this->hasMany('App\TenantContract');
    }

    /**
     * Get all the tenant contracts that is currently active of this room.
     */
    public function activeContracts() {
        return $this->hasMany('App\TenantContract')->active();
    }

    /**
     * Get all the tenants who ever lived in this room.
     */
    public function tenantsHistory() {
        return $this->belongsToMany('App\Tenant', 'App\TenantContract');
    }

    /**
     * Get all keys of this room.
     */
    public function keys() {
        return $this->hasMany('App\Key');
    }

    /**
     * Get all appliances of this room.
     */
    public function appliances() {
        return $this->hasMany('App\Appliance');
    }

    /**
     * Get all of the room's pictures.
     * 照片
     */
    public function pictures() {
        return $this->morphMany('App\Document', 'attachable');
    }

    /**
     * Get the landlord payments of this room.
     */
    public function landlordPayments() {
        return $this->hasMany('App\LandlordPayment');
    }
    
    /**
     * Get all landlord other subjects of this building.
     */
    public function landlordOtherSubjects() {
        return $this->hasMany('App\LandlordOtherSubject');
    }
}
