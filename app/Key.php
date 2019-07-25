<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Key extends Model
{
    
    use SoftDeletes;

    /**
     * Get the key's keeper 
     */
    public function keeper() {
        return $this->belongsTo('App\User', 'keeper_id');
    }

    /**
     * Get the room of this key.
     */
    public function room() {
        return $this->belongsTo('App\Room');
    }

    /**
     * Get all the request history of this key.
     */
    public function keyRequests() {
        return $this->hasMany('App\KeyRequest');
    }
}
