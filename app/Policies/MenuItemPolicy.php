<?php

namespace App\Policies;

use App\Models\MenuItem;
use App\Models\User;

class MenuItemPolicy
{
    public function update(User $user, MenuItem $menuItem): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $this->isOwnedByTenant($user, $menuItem->tenant_id ?? null);
    }

    public function delete(User $user, MenuItem $menuItem): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $this->isOwnedByTenant($user, $menuItem->tenant_id ?? null);
    }

    private function isOwnedByTenant(User $user, mixed $tenantId): bool
    {
        if (!$user->isTenant() || empty($tenantId)) {
            return false;
        }

        $userTenantId = data_get($user, 'tenant_id');
        if (empty($userTenantId)) {
            return false;
        }

        return (int) $userTenantId === (int) $tenantId;
    }
}
