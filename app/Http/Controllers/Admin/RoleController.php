<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Services\Admin\RoleService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoleController extends Controller
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function index()
    {
        return Inertia::render('Admin/Roles', [
            'roles' => $this->roleService->getAllRoles(),
            'permissions' => Permission::all()->groupBy('group'),
        ]);
    }

    public function store(Request $request)
    {
        if (!$request->user()->isSuperAdmin()) abort(403);
        $data = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'label' => 'required|string',
            'description' => 'nullable|string',
            'permissions' => 'array',
        ]);

        $this->roleService->createRole($data);

        return redirect()->back()->with('success', 'Security role created.');
    }

    public function update(Request $request, Role $role)
    {
        if (!$request->user()->isSuperAdmin()) abort(403);
        $data = $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'label' => 'required|string',
            'description' => 'nullable|string',
            'permissions' => 'array',
        ]);

        $this->roleService->updateRole($role, $data);

        return redirect()->back()->with('success', 'Security role updated.');
    }

    public function destroy(Request $request, Role $role)
    {
        if (!$request->user()->isSuperAdmin()) abort(403);
        try {
            $this->roleService->deleteRole($role);
            return redirect()->back()->with('success', 'Security role purged.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
