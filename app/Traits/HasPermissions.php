<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;
use \Spatie\Permission\Traits\HasPermissions as HasPermissionsBase;

// add/override method for `group` use case for better readability
trait HasPermissions {
    use HasPermissionsBase;

    /**
     * A model may have multiple direct permissions.
     */
    public function permissions(): MorphToMany
    {
        return $this->morphToMany(
            config('permission.models.permission'),
            'model',
            config('permission.table_names.model_has_permissions'),
            config('permission.column_names.model_morph_key'),
            'permission_id'
        );
    }

    // Delegate for better readability
    public function getPermissionsViaGroups(): Collection
    {
        return $this->getPermissionsViaRoles();
    }
}
