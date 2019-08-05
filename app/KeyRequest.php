<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Carbon\Carbon;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class KeyRequest extends Model implements AuditableContract
{
    use SoftDeletes;
    use AuditableTrait;

    /**
     * Get the the user who made this request.
     */
    public function requestUser() {
        return $this->belongsTo('App\User', 'request_user_id');
    }

    /**
     * Get the requested key.
     */
    public function key() {
        return $this->belongsTo('App\Key');
    }

    /**
     * Scope a query to only include approved key requests.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query) {
        return $query->where('request_approved', true);
    }

    /**
     * Scope a query to only include denied key requests.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDenied($query) {
        return $query->where('request_approved', false);
    }
}
