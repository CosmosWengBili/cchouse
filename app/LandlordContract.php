<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class LandlordContract extends Model implements AuditableContract
{
    use SoftDeletes;
    use AuditableTrait;

    protected $guarded = [];
    protected $hidden = ['pivot'];

    protected $appends = array('landlord_ids');

    public function getLandlordIdsAttribute() {
        return implode(",",$this->landlords()->get()->pluck('id')->toArray());
    }

    /**
     * Get the building of this landlord contract.
     */
    public function building()
    {
        return $this->belongsTo('App\Building');
    }

    /**
     * Get the landlord of this contract.
     */
    public function landlords()
    {
        return $this->belongsToMany('App\Landlord');
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
     * Get the commissioner of this contract.
     */
    public function commissioner()
    {
        return $this->belongsTo('App\User', 'commissioner_id');
    }
}
