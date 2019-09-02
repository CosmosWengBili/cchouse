<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class LandlordPayment extends Model implements AuditableContract
{
    use SoftDeletes;
    use AuditableTrait;

    protected $fillable = [
        'room_id',
        'subject',
        'bill_serial_number',
        'bill_start_date',
        'bill_end_date',
        'collection_date',
        'billing_vendor',
        'amount',
        'comment'
    ];
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
