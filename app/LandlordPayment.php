<?php

namespace App;

use App\Traits\WithExtraInfo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class LandlordPayment extends Model implements AuditableContract
{
    use SoftDeletes;
    use AuditableTrait;
    use WithExtraInfo;

    protected $guarded = [];

    protected $hidden = ['pivot', 'deleted_at'];
    /**
     * Get the room of this landlord payment.
     */
    public function room()
    {
        return $this->belongsTo('App\Room');
    }

    /**
     * Get the receipts of this landlord payment.
     */
    public function receipts()
    {
        return $this->morphToMany('App\Receipt', 'receiptable');
    }
}
