<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;
use \Spatie\Permission\Traits\HasPermissions as HasPermissionsBase;

// add/override method for `group` use case for better readability
trait HasPermissions {
    use HasPermissionsBase;

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

    /**
     * A role belongs to some users of the model associated with its guard.
     */
    public function users(): MorphToMany
    {
        return $this->morphedByMany(
            getModelForGuard($this->attributes['guard_name']),
            'model',
            config('permission.table_names.model_has_roles'),
            'group_id',
            config('permission.column_names.model_morph_key')
        );
    }

    // Delegate for better readability
    public function getPermissionsViaGroups(): Collection
    {
        return $this->getPermissionsViaRoles();
    }
}
