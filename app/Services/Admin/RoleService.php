<?php

namespace App\Services\Admin;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityLog;

class RoleService
{
    public function getAllRoles()
    {
        return Role::withCount(['users', 'permissions'])->get();
    }

    public function createRole(array $data)
    {
        return DB::transaction(function () use ($data) {
            $role = Role::create([
                'name' => $data['name'],
                'label' => $data['label'],
                'description' => $data['description'] ?? null,
            ]);

            if (!empty($data['permissions'])) {
                $role->permissions()->sync($data['permissions']);
            }

            $this->logActivity('created_role', Role::class, $role->id, $data);

            return $role;
        });
    }

    public function updateRole(Role $role, array $data)
    {
        return DB::transaction(function () use ($role, $data) {
            $oldData = $role->load('permissions')->toArray();
            
            $role->update([
                'name' => $data['name'],
                'label' => $data['label'],
                'description' => $data['description'] ?? null,
            ]);

            if (isset($data['permissions'])) {
                $role->permissions()->sync($data['permissions']);
            }

            $this->logActivity('updated_role', Role::class, $role->id, [
                'old' => $oldData,
                'new' => $data
            ]);

            return $role;
        });
    }

    public function deleteRole(Role $role)
    {
        if ($role->users()->exists()) {
            throw new \Exception("Cannot delete role assigned to users.");
        }

        return DB::transaction(function () use ($role) {
            $roleId = $role->id;
            $roleData = $role->toArray();
            $role->delete();

            $this->logActivity('deleted_role', Role::class, $roleId, $roleData);

            return true;
        });
    }

    protected function logActivity($action, $modelType, $modelId, $payload)
    {
        ActivityLog::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'payload' => $payload,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
