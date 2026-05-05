<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function view(User $user, Order $order): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $this->isOwnedByTenant($user, $order->tenant_id ?? null);
    }

    public function update(User $user, Order $order): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $this->isOwnedByTenant($user, $order->tenant_id ?? null);
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
