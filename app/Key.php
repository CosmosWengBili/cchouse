<?php

namespace App;

use App\Traits\WithExtraInfo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Key extends Model implements AuditableContract
{
    use SoftDeletes;
    use AuditableTrait;
    use WithExtraInfo;

    protected $guarded = [];
    protected $hidden = ['pivot', 'deleted_at'];
    /**
     * Get the key's keeper
     */
    public function keeper()
    {
        return $this->belongsTo('App\User', 'keeper_id');
    }

    /**
     * Get the room of this key.
     */
    public function room()
    {
        return $this->belongsTo('App\Room');
    }

    /**
     * Get all the request history of this key.
     */
    public function keyRequests()
    {
        return $this->hasMany('App\KeyRequest');
    }

    public function documents()
    {
        return $this->morphMany('App\Document', 'attachable');
    }

    /**
     * Get key's documents.
     * 鑰匙相關照片
     */
    public function keyDocuments()
    {
        return $this->documents()->where('document_type', 'key_file');
    }
}
