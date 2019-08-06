<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Building extends Model implements AuditableContract
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
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [ 'water_meter_reading_date' ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'has_elevator' => 'boolean',
    ];

    /**
     * Get the user who is the commissioner of this building.
     */
    public function commissioner() {
        return $this->belongsTo('App\User', 'commissioner_id');
    }

    /**
     * Get the user who is the administrator of this building.
     */
    public function administrator() {
        return $this->belongsTo('App\User', 'administrator_id');
    }

    /**
     * Get the rooms of this building.
     */
    public function rooms() {
        return $this->hasMany('App\Room');
    }

    /**
     * Get the landlord contract of this building.
     */
    public function landlordContracts() {
        return $this->hasMany('App\LandlordContract');
    }

    /**
     * Get the shareholders of this building.
     */
    public function shareholders()
    {
        return $this->belongsToMany('App\Shareholder');
    }
}
