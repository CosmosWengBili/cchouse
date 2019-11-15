<?php

namespace App;

use Eloquent as Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

/**
 * Class PayOffDetail
 * @package App
 * @version November 14, 2019, 2:01 pm CST
 *
 * @property integer pay_off_id
 * @property string detail_type
 * @property integer detail_id
 */
class PayOffDetail extends Model implements AuditableContract
{
    use AuditableTrait;

    public $table = 'pay_off_details';

    public $timestamps = false;

    protected $dates = ['deleted_at'];

    protected $guarded = [];

    protected $fillable = [
        'pay_off_id',
        'detail_type',
        'detail_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'pay_off_id' => 'integer',
        'detail_type' => 'string',
        'detail_id' => 'integer'
    ];

    public function detail()
    {
        return $this->morphTo();
    }
}
