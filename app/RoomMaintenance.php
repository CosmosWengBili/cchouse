<?php

namespace App;

use App\Traits\WithExtraInfo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

use App\Traits\Controllers\HandleDocumentsUpload;

/**
 * Class RoomMaintenance
 * @package App\Models
 * @version November 1, 2019, 4:42 pm CST
 *
 * @property \App\Room room
 * @property integer room_id
 * @property string maintainer
 * @property string maintained_location
 * @property string maintained_date
 */
class RoomMaintenance extends Model implements AuditableContract
{
    use HandleDocumentsUpload;
    use AuditableTrait;
    use SoftDeletes;
    use WithExtraInfo;

    // public $table = 'room_maintenances';

    // public $timestamps = false;

    protected $hidden = ['deleted_at'];

    protected $guarded = [];

    protected $fillable = [
        'id',
        'room_id',
        'maintainer',
        'maintained_location',
        'maintained_date',
        'pictures'
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
        'maintained_date' => 'date:Y-m-d',
        'pictures' => 'array'
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

    public function setPicturesAttribute($value)
    {
        $this->handleDocumentsUploadByArray($this, $value, 'picture');
        unset($this->pictures);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function room()
    {
        return $this->belongsTo(\App\Room::class, 'room_id');
    }

    public function documents()
    {
        return $this->morphMany('App\Document', 'attachable');
    }

    public function pictures()
    {
        return $this->documents()->where('document_type', 'picture');
    }
}
