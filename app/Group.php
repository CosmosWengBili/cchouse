<?php
namespace App;

use App\Traits\HasPermissions;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Role as RoleBase;

class Group extends RoleBase
{
    use HasPermissions;


    public function department() {
        return $this->belongsTo('App\Department');
    }

    /**
     * A role may be given various permissions.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            config('permission.models.permission'),
            config('permission.table_names.role_has_permissions'),
            'group_id',
            'permission_id'
        );
    }
}