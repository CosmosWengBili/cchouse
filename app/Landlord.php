<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Landlord extends Model implements AuditableContract
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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_legal_person' => 'boolean',
        'is_collected_by_third_party' => 'boolean'
    ];

    /**
     * Get all the landlord's contracts.
     */
    public function landlordContracts()
    {
        return $this->belongsToMany('App\LandlordContract');
    }

    /**
     * Get all the landlord's contracts.
     */
    public function activeContracts()
    {
        return $this->landlordContracts()->active();
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
     * 代收文件
     */
    public function thirdPartyDocuments()
    {
        return $this->documents()->where('document_type', 'third_party_file');
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
     * Get a list of the landlords's phone numbers.
     * 聯絡電話
     */
    public function phones()
    {
        return $this->contactInfos()->where('info_type', 'phone');
    }

    /**
     * Get a list of the landlords's mailing addresses.
     * 聯絡地址
     */
    public function mailingAddresses()
    {
        return $this->contactInfos()->where('info_type', 'mailing_address');
    }

    /**
     * Get a list of the landlords's emails.
     * 電子郵件
     */
    public function emails()
    {
        return $this->contactInfos()->where('info_type', 'email');
    }

    /**
     * Get a list of the landlords's fax numbers.
     * 傳真
     */
    public function faxNumbers()
    {
        return $this->contactInfos()->where('info_type', 'fax_number');
    }

    /**
     * Get all of the landlord's agents.
     * 代理人
     */
    public function agents()
    {
        return $this->hasMany('App\LandlordAgent');
    }
}
