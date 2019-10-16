<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class LandlordOtherSubject extends Model implements AuditableContract
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
     * Get the room of this landlord other subject.
    */
    public function room()
    {
        return $this->belongsTo('App\Room');
    }

    /**
     * Get the receipts of this landlord other subject.
     */
    public function receipts()
    {
        return $this->morphMany('App\Receipt', 'receiptable');
    }
}
