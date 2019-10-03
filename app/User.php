<?php

namespace App;

use App\Traits\HasGroups;
use App\Traits\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class User extends Authenticatable implements AuditableContract
{
    use Notifiable;
    use SoftDeletes;
    use HasGroups;
    use AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['email_verified_at', 'remember_token', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime'
    ];

    /**
     * Get the buildings this user commissions.
     */
    public function commissionBuildings()
    {
        return $this->hasMany('App\Building', 'commissioner_id');
    }

    /**
     * Get the buildings this user manages.
     */
    public function manageBuildings()
    {
        return $this->hasMany('App\Building', 'administrator_id');
    }

    /**
     * Get the tenants whose information was confirmed by this user.
     */
    public function confirmTenants()
    {
        return $this->hasMany('App\Tenant', 'confirm_by');
    }

    /**
     * Get the tenant contracts this user commissions.
     */
    public function commissionTenantContracts()
    {
        return $this->hasMany('App\TenantContract', 'commissioner_id');
    }

    /**
     * Get the keys this user keeps.
     */
    public function keys()
    {
        return $this->hasMany('App\Key', 'keeper_id');
    }

    /**
     * Get the keys requests this user ever made.
     */
    public function keyRequests()
    {
        return $this->hasMany('App\KeyRequest', 'request_user_id');
    }

    /**
     * Get the debt collections this user ever made.
     */
    public function debtCollections()
    {
        return $this->hasMany('App\DebtCollection', 'colloector_id');
    }

    /**
     * Get the maintenances this user commissions.
     */
    public function commissionMaintenances()
    {
        return $this->hasMany('App\Maintenance', 'commissioner_id');
    }

    /**
     * Get the maintenances this user made.
     */
    public function maintenances()
    {
        return $this->hasMany('App\Maintenance', 'maintenance_staff_id');
    }

    /**
     * Get all the landlord contracts this user commissions.
     */
    public function landlordContracts()
    {
        return $this->hasMany('App\LandlordContract', 'commissioner_id');
    }
    /**
     * Get all the audits of this user.
     */
    public function allAudits()
    {
        return $this->hasMany('App\Audit', 'user_id')->where(
            'user_type',
            static::class
        );
    }

    /**
     * Check whether user is in the group
     *
     * @param string $groupName
     * @return boolean
     */
    public function belongsToGroup(string $groupName)
    {
        return $this->getGroupNames()->contains($groupName);
    }

    /**
     * Check whether user is in the department
     *
     * @return boolean
     */
    public function belongsToDepartment(string $departmentName)
    {
        $departmentIds = $this->groups()->pluck('department_id');
        $departmentNames = Department::whereIn('id', $departmentIds)->pluck(
            'name'
        );

        return $departmentNames->contains($departmentName);
    }
}
