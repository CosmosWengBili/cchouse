<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Carbon\Carbon;

class LandlordContract extends Model implements AuditableContract
{
    use SoftDeletes;
    use AuditableTrait;

    protected $hidden = ['pivot'];
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

    /**
     * Scope a query to only include active landlord contracts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query
            ->where('commission_end_date', '>', Carbon::today());
    }
}
