<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class MonthlyReport extends Model implements AuditableContract
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
     * Get the landlordContract that this monthly report is in.
     */
    public function landlordContract()
    {
        return $this->belongsTo('App\LandlordContract');
    }
}
