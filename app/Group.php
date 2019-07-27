<?php

namespace App;
use App\Traits\HasPermissions;
use Spatie\Permission\Models\Role as RoleBase;

class Group extends RoleBase
{
    use HasPermissions;
}
