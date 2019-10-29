<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Tenant extends Model implements AuditableContract
{
    use SoftDeletes;
    use AuditableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['emergency_contact', 'guarantor', 'contact_infos'];

    protected $hidden = ['pivot', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_legal_person' => 'boolean'
    ];

    /**
     * Get all the contracts of this tenant.
     */
    public function tenantContracts()
    {
        return $this->hasMany('App\TenantContract');
    }

    /**
     * Get all the currently active contracts of this tenant.
     */
    public function activeContracts()
    {
        return $this->hasMany('App\TenantContract')->active();
    }

    /**
     * Get all the rooms that this tenant ever lived in.
     */
    public function roomsHistory()
    {
        return $this->belongsToMany('App\Room', 'App\TenantContract');
    }

    /**
     * Get the user who confirmed the tenant's information.
     */
    public function confirmBy()
    {
        return $this->belongsTo('App\User', 'confirm_by');
    }

    /**
     * Get all kinds of the landlords's contact infos.
     * 所有聯絡資訊
     */
    public function contactInfos()
    {
        return $this->morphMany('App\ContactInfo', 'contactable');
    }

    /**
     * Get a list of the tenant's phone numbers.
     * 聯絡電話
     */
    public function phones()
    {
        return $this->contactInfos()->where('info_type', 'phone');
    }

    /**
     * Get a list of the tenant's mailing addresses.
     * 聯絡地址
     */
    public function mailingAddresses()
    {
        return $this->contactInfos()->where('info_type', 'mailing_address');
    }

    /**
     * Get a list of the tenant's emails.
     * 電子郵件
     */
    public function emails()
    {
        return $this->contactInfos()->where('info_type', 'email');
    }

    /**
     * Get all kinds of the tenant's related people.
     * 所有關係人
     */
    public function relatedPeople()
    {
        return $this->morphMany('App\RelatedPerson', 'related_person');
    }

    /**
     * Get all tenant's emergency contacts.
     * 所有緊急聯絡人
     */
    public function emergencyContacts()
    {
        return $this->relatedPeople()->where('type', 'emergency_contact');
    }

    /**
     * Get all tenant's guarantor.
     * 所有保證人
     */
    public function guarantors()
    {
        return $this->relatedPeople()->where('type', 'guarantor');
    }
}
