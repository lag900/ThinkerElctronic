<?php

namespace App\Services\Admin;

use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityLog;

class PermissionService
{
    public function getAllPermissions()
    {
        return Permission::all()->groupBy('group');
    }

    public function createPermission(array $data)
    {
        return DB::transaction(function () use ($data) {
            $permission = Permission::create([
                'name' => $data['name'],
                'label' => $data['label'],
                'group' => $data['group'],
            ]);

            $this->logActivity('created_permission', Permission::class, $permission->id, $data);

            return $permission;
        });
    }

    protected function logActivity($action, $modelType, $modelId, $payload)
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'payload' => $payload,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
