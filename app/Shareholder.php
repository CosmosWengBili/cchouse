<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shareholder extends Model
{

    use SoftDeletes;

    /**
     * Get the buildings of this shareholder.
     */
    public function buildings()
    {
        return $this->belongsToMany('App\Building');
    }
}
