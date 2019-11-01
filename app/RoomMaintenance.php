<?php

namespace App;

use Eloquent as Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class RoomMaintenance
 * @package App\Models
 * @version November 1, 2019, 4:42 pm CST
 *
 * @property \App\Models\Room room
 * @property integer room_id
 * @property string maintainer
 * @property string maintained_location
 * @property string maintained_date
 */
class RoomMaintenance extends Model implements AuditableContract
{
    use AuditableTrait;
    use SoftDeletes;

    public $table = 'room_maintenances';

    // public $timestamps = false;

    protected $dates = ['deleted_at'];

    protected $guarded = [];

    protected $fillable = [
        'room_id',
        'maintainer',
        'maintained_location',
        'maintained_date'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'room_id' => 'integer',
        'maintainer' => 'string',
        'maintained_location' => 'string',
        'maintained_date' => 'date'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'room_id' => 'required',
        'maintainer' => 'required',
        'maintained_location' => 'required',
        'maintained_date' => 'required'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function room()
    {
        return $this->belongsTo(\App\Room::class, 'room_id');
    }
}
