<?php

namespace App;

use App\Scopes\ExtraBuildingInfoScope;
use App\Traits\ExtraInfo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Carbon\Carbon;

class LandlordContract extends Model implements AuditableContract
{
    use SoftDeletes;
    use AuditableTrait;
    use ExtraInfo;

    protected $guarded = [];

    protected $hidden = ['pivot', 'deleted_at'];

    protected $casts = ['commission_start_date' => 'date',
                        'commission_end_date' => 'date'];

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

    /**
     * Get the monthlyReports of this contract.
     */
    public function monthlyReports()
    {
        return $this->hasMany('App\MonthlyReport');
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
            ->where('commission_start_date', '<', Carbon::today())
            ->where('commission_end_date', '>', Carbon::today())
            ->first();
    }
}
