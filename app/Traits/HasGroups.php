<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;
use Spatie\Permission\Contracts\Role;
use Spatie\Permission\Traits\HasRoles As HasRoleBase;

// add/override method for `group` use case for better readability
trait HasGroups {
    /*
     * Use custom HasPermissions
     *
     * @TODO: Find better way to override trait
     */
    use HasRoleBase, HasPermissions {
        HasPermissions::bootHasPermissions insteadof HasRoleBase;
        HasPermissions::getPermissionClass insteadof HasRoleBase;
        HasPermissions::permissions insteadof HasRoleBase;
        HasPermissions::scopePermission insteadof HasRoleBase;
        HasPermissions::convertToPermissionModels insteadof HasRoleBase;
        HasPermissions::hasPermissionTo insteadof HasRoleBase;
        HasPermissions::hasUncachedPermissionTo insteadof HasRoleBase;
        HasPermissions::checkPermissionTo insteadof HasRoleBase;
        HasPermissions::hasAnyPermission insteadof HasRoleBase;
        HasPermissions::hasAllPermissions insteadof HasRoleBase;
        HasPermissions::hasPermissionViaRole insteadof HasRoleBase;
        HasPermissions::hasDirectPermission insteadof HasRoleBase;
        HasPermissions::getPermissionsViaRoles insteadof HasRoleBase;
        HasPermissions::getAllPermissions insteadof HasRoleBase;
        HasPermissions::givePermissionTo insteadof HasRoleBase;
        HasPermissions::syncPermissions insteadof HasRoleBase;
        HasPermissions::revokePermissionTo insteadof HasRoleBase;
        HasPermissions::getPermissionNames insteadof HasRoleBase;
        HasPermissions::getStoredPermission insteadof HasRoleBase;
        HasPermissions::ensureModelSharesGuard insteadof HasRoleBase;
        HasPermissions::getGuardNames insteadof HasRoleBase;
        HasPermissions::getDefaultGuardName insteadof HasRoleBase;
        HasPermissions::forgetCachedPermissions insteadof HasRoleBase;
    }

    /**
     * A model may have multiple groups.
     */
    public function groups(): MorphToMany
    {
        return $this->morphToMany(
            config('permission.models.role'),
            'model',
            config('permission.table_names.model_has_roles'),
            config('permission.column_names.model_morph_key'),
            'group_id'
        );
    }

    /**
     * Override for internal use in `HasRoles` trait
     */
    public function roles(): MorphToMany
    {
        return $this->groups();
    }

    /**
     * Add methods for `group` use case
     * @param Builder $query
     * @param string|array|Role|Collection $groups
     * @param string $guard
     *
     * @return Builder
     */
    public function scopeGroup(Builder $query, $groups, $guard = null): Builder
    {
        return $this->scopeRole($query, $groups, $guard);
    }

    public function assignGroup(...$groups)
    {
        return $this->assignRole($groups);
    }

    public function removeGroup($group)
    {
        return $this->removeRole($group);
    }

    public function syncGroups(...$groups)
    {
        return $this->syncRoles($groups);
    }

    public function hasGroup($group): bool
    {
        return $this->hasRole($group);
    }

    public function hasAnyGroup($group): bool
    {
        return $this->hasAnyRole($group);
    }

    public function hasAllGroups($groups): bool
    {
        return $this->hasAllRoles($groups);
    }

    public function getGroupNames(): Collection
    {
        return $this->getRoleNames();
    }
}
