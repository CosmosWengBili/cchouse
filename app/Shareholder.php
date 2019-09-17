<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Shareholder extends Model implements AuditableContract
{
    use SoftDeletes;
    use AuditableTrait;

    protected $guarded = [];
    
    protected $hidden = ['pivot', 'deleted_at'];

    protected $casts = ['distribution_start_date' => 'date',
                        'distribution_end_date' => 'date'];

    protected $appends = array('building_ids');

    public function getBuildingIdsAttribute() {
        return implode(",",$this->buildings()->get()->pluck('id')->toArray());
    }
    /**
     * Get the buildings of this shareholder.
     */
    public function buildings()
    {
        return $this->belongsToMany('App\Building');
    }
}
