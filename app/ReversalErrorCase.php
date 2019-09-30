<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReversalErrorCase extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function payLog()
    {
        return $this->belongsTo('App\PayLog', 'pay_log_id');
    }
}