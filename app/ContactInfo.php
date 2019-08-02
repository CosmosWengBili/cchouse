<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class ContactInfo extends Model implements AuditableContract
{
    use AuditableTrait;

    protected $guarded = [];

    protected $hidden = ['pivot'];
}
