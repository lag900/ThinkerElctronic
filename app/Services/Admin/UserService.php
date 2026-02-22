<?php

namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityLog;

class UserService
{
    public function getAllUsers($filters = [])
    {
        return User::with('roleNode')
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($filters['role_id'] ?? null, function ($query, $roleId) {
                $query->where('role_id', $roleId);
            })
            ->when($filters['status'] ?? null, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(15);
    }

    public function createUser(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role_id' => $data['role_id'] ?? null,
                'status' => $data['status'] ?? 'active',
            ]);

            $this->logActivity('created_user', User::class, $user->id, $data);

            return $user;
        });
    }

    public function updateUser(User $user, array $data)
    {
        return DB::transaction(function () use ($user, $data) {
            $updateData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'role_id' => $data['role_id'] ?? $user->role_id,
                'status' => $data['status'] ?? $user->status,
            ];

            if (!empty($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            $oldData = $user->toArray();
            $user->update($updateData);

            $this->logActivity('updated_user', User::class, $user->id, [
                'old' => $oldData,
                'new' => $data
            ]);

            return $user;
        });
    }

    public function deleteUser(User $user)
    {
        return DB::transaction(function () use ($user) {
            $userId = $user->id;
            $userData = $user->toArray();
            $user->delete();

            $this->logActivity('deleted_user', User::class, $userId, $userData);

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
