<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    /**
     * Get the buildings this user commissions.
     */
    public function commissionBuildings() {
        return $this->hasMany('App\Building', 'commissioner_id');
    }
    
    /**
     * Get the buildings this user manages.
     */
    public function manageBuildings() {
        return $this->hasMany('App\Building', 'administrator_id');
    }

    /**
     * Get the tenants whose information was confirmed by this user.
     */
    public function confirmTenants() {
        return $this->hasMany('App\Tenant', 'confirm_by');
    }

    /**
     * Get the tenant contracts this user commissions.
     */
    public function commissionTenantContracts() {
        return $this->hasMany('App\TenantContract', 'commissioner_id');
    }

    /**
     * Get the keys this user keeps.
     */
    public function keys() {
        return $this->hasMany('App\Key', 'keeper_id');
    }

    /**
     * Get the keys requests this user ever made.
     */
    public function keyRequests() {
        return $this->hasMany('App\KeyRequest', 'request_user_id');
    }

    /**
     * Get the debt collections this user ever made.
     */
    public function debtCollections() {
        return $this->hasMany('App\DebtCollection', 'colloector_id');
    }

    /**
     * Get the maintenances this user commissions.
     */
    public function commissionMaintenances() {
        return $this->hasMany('App\Maintenance', 'commissioner_id');
    }

    /**
     * Get the maintenances this user made.
     */
    public function maintenances() {
        return $this->hasMany('App\Maintenance', 'maintenance_staff_id');
    }

    /**
     * Get all the landlord contracts this user commissions.
     */
    public function landlordContracts() {
        return $this->hasMany('App\LandlordContract', 'commissioner_id');
    }
}
