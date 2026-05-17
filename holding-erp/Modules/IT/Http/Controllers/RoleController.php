<?php

namespace Modules\IT\Http\Controllers;

use App\Core\Activity\ActivityLogger;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Modules\Audit\Models\ActivityLog;
use Modules\Audit\Models\Permission;
use Modules\Audit\Models\Role;
use Modules\IT\Http\Requests\StoreRoleRequest;
use Modules\IT\Http\Requests\UpdateRoleRequest;
use Modules\IT\Services\RoleManagementService;

class RoleController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Role::class);

        return view('it::roles.index', [
            'roles' => Role::query()
                ->withCount(['users', 'permissions'])
                ->orderBy('scope_level')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Role::class);

        return view('it::roles.create', $this->formData());
    }

    public function store(
        StoreRoleRequest $request,
        RoleManagementService $roleService,
        ActivityLogger $activityLogger,
    ): RedirectResponse {
        Gate::authorize('create', Role::class);

        $role = $roleService->create($request->validated());

        $activityLogger->log(
            'it.role.created',
            $request->user(),
            $role,
            metadata: ['role_code' => $role->code, 'permissions_count' => $role->permissions->count()],
            newValues: $this->auditValues($role),
            request: $request,
        );

        return redirect()
            ->route('it.roles.show', $role)
            ->with('status', 'Role berhasil dibuat dan permission sudah disinkronkan.');
    }

    public function show(Role $role): View
    {
        Gate::authorize('view', $role);

        return view('it::roles.show', [
            'role' => $role->load(['permissions' => fn ($query) => $query->orderBy('module')->orderBy('action')])->loadCount('users'),
            'activityLogs' => ActivityLog::query()
                ->with('user')
                ->forSubject(Role::class, $role->id)
                ->latest()
                ->limit(10)
                ->get(),
        ]);
    }

    public function edit(Role $role): View
    {
        Gate::authorize('update', $role);

        return view('it::roles.edit', $this->formData(['role' => $role->load('permissions')]));
    }

    public function update(
        UpdateRoleRequest $request,
        Role $role,
        RoleManagementService $roleService,
        ActivityLogger $activityLogger,
    ): RedirectResponse {
        Gate::authorize('update', $role);

        $role->load('permissions');
        $oldValues = $this->auditValues($role);
        $role = $roleService->update($role, $request->validated());

        $activityLogger->log(
            'it.role.updated',
            $request->user(),
            $role,
            metadata: ['role_code' => $role->code, 'permissions_count' => $role->permissions->count()],
            oldValues: $oldValues,
            newValues: $this->auditValues($role),
            request: $request,
        );

        return redirect()
            ->route('it.roles.show', $role)
            ->with('status', 'Role dan permission berhasil diperbarui.');
    }

    /**
     * @param array<string, mixed> $extra
     * @return array<string, mixed>
     */
    private function formData(array $extra = []): array
    {
        return $extra + [
            'role' => $extra['role'] ?? new Role(),
            'permissionGroups' => Permission::query()
                ->orderBy('module')
                ->orderBy('action')
                ->get()
                ->groupBy('module'),
            'scopeLevels' => ['holding', 'city', 'brand', 'branch', 'warehouse'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function auditValues(Role $role): array
    {
        return [
            'code' => $role->code,
            'name' => $role->name,
            'scope_level' => $role->scope_level,
            'is_system' => (bool) $role->is_system,
            'permissions' => $role->permissions->pluck('code')->values()->all(),
        ];
    }
}
