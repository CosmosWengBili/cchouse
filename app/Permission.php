<?php
namespace App;

use App\Traits\HasGroups;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Permission as PermissionBase;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Permission extends PermissionBase implements AuditableContract
{
    use HasGroups;
    use AuditableTrait;


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
