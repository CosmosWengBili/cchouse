<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Deposit extends Model implements AuditableContract
{
    use SoftDeletes;
    use AuditableTrait;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $hidden = ['pivot'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_deposit_collected' => 'boolean',
    ];

    /**
     * Get the tenant contract of deposit.
     */
    public function tenantContract()
    {
        return $this->belongsTo('App\TenantContract');
    }
}
