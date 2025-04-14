<?php

namespace App\Policies;

use App\Models\OrdenVenta;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrdenVentaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        $permisos = $user->getPermisos();
        return $user->permisos == '*' || (is_array($permisos) && in_array('orden_ventas.index', $permisos));
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OrdenVenta $ordenVenta): bool
    {
        $permisos = $user->getPermisos();
        $sw_policy =  $user->permisos == '*' || (is_array($permisos) && in_array('orden_ventas.show', $permisos));
        if ($user->role == 2) {
            $sw_policy = $ordenVenta->cliente_id === ($user->cliente ?? $user->cliente->id);
        }

        return $sw_policy;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role == 2;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OrdenVenta $ordenVenta): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OrdenVenta $ordenVenta): bool
    {
        $permisos = $user->getPermisos();
        return  $user->permisos == '*' || (is_array($permisos) && in_array('orden_ventas.destroy', $permisos));
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, OrdenVenta $ordenVenta): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, OrdenVenta $ordenVenta): bool
    {
        return false;
    }
}
