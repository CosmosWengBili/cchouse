<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Appliance extends Model implements AuditableContract
{
    use SoftDeletes;
    use AuditableTrait;

    protected $guarded = [];
    /**
     * Get the room this appliance belongs to.
     */
    public function room()
    {
        return $this->belongsTo('App\Room');
    }
}
