<?php
namespace App;

use App\Traits\HasGroups;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Permission as PermissionBase;

class Permission extends PermissionBase
{
    use HasGroups;


    /**
     * A permission can be applied to roles.
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(
            config('permission.models.role'),
            config('permission.table_names.role_has_permissions'),
            'permission_id',
            'group_id'
        );
    }

    public function roles(): BelongsToMany
    {
        return $this->groups();
    }
}
