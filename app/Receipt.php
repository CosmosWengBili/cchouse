<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Receipt extends Model implements AuditableContract
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
     * Get the pay logs.
     */
    public function payLogs()
    {
        return $this->morphedByMany('App\PayLog', 'receiptable');
    }

    /**
     * Get the maintenances.
     */
    public function maintenances()
    {
        return $this->morphedByMany('App\Maintenance', 'receiptable');
    }

    /**
     * Get the landlordOtherSubjects.
     */
    public function landlordOtherSubjects()
    {
        return $this->morphedByMany('App\LandlordOtherSubject', 'receiptable');
    }

    /**
     * Get the companyIncomes.
     */
    public function companyIncomes()
    {
        return $this->morphedByMany('App\CompanyIncome', 'receiptable');
    }
}
