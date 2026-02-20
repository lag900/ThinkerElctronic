<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Services\Admin\PermissionService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PermissionController extends Controller
{
    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    public function index()
    {
        return Inertia::render('Admin/Permissions', [
            'permissions' => $this->permissionService->getAllPermissions(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|unique:permissions,name',
            'label' => 'required|string',
            'group' => 'required|string',
        ]);

        $this->permissionService->createPermission($data);

        return redirect()->back()->with('success', 'Hardware protocol permission granted.');
    }
}
