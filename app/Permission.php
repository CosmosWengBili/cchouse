<?php

namespace App;

use App\Traits\HasGroups;
use Spatie\Permission\Models\Permission as PermissionBase;

class Permission extends PermissionBase
{
    use HasGroups;
}
