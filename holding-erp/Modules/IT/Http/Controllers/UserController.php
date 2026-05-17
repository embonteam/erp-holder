<?php

namespace Modules\IT\Http\Controllers;

use App\Core\Activity\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Modules\Audit\Models\ActivityLog;
use Modules\Audit\Models\Role;
use Modules\Holding\Models\Brand;
use Modules\Holding\Models\Branch;
use Modules\Holding\Models\City;
use Modules\Holding\Models\Holding;
use Modules\Holding\Models\HoldingCityPosition;
use Modules\Holding\Models\Warehouse;
use Modules\IT\Http\Requests\StoreManagedUserRequest;
use Modules\IT\Http\Requests\UpdateManagedUserRequest;
use Modules\IT\Services\ManagedUserService;

class UserController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', User::class);

        return view('it::users.index', [
            'users' => User::query()
                ->with(['role', 'holding', 'brand', 'city', 'branch', 'warehouse'])
                ->orderByDesc('is_active')
                ->orderBy('name')
                ->paginate(25),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', User::class);

        return view('it::users.create', $this->formData());
    }

    public function store(
        StoreManagedUserRequest $request,
        ManagedUserService $managedUserService,
        ActivityLogger $activityLogger,
    ): RedirectResponse {
        Gate::authorize('create', User::class);

        $managedUser = $managedUserService->create($request->validated());

        $activityLogger->log(
            'it.user.created',
            $request->user(),
            $managedUser,
            metadata: ['email' => $managedUser->email, 'role_id' => $managedUser->role_id],
            newValues: $this->auditValues($managedUser),
            request: $request,
        );

        return redirect()
            ->route('it.users.show', $managedUser)
            ->with('status', 'User berhasil dibuat dengan role dan scope enterprise.');
    }

    public function show(User $user): View
    {
        Gate::authorize('view', $user);

        return view('it::users.show', [
            'managedUser' => $user->load(['role', 'holding', 'holdingCityPosition', 'brand', 'city', 'branch', 'warehouse']),
            'activityLogs' => ActivityLog::query()
                ->with('user')
                ->forSubject(User::class, $user->id)
                ->latest()
                ->limit(10)
                ->get(),
        ]);
    }

    public function edit(User $user): View
    {
        Gate::authorize('update', $user);

        return view('it::users.edit', $this->formData(['managedUser' => $user]));
    }

    public function update(
        UpdateManagedUserRequest $request,
        User $user,
        ManagedUserService $managedUserService,
        ActivityLogger $activityLogger,
    ): RedirectResponse {
        Gate::authorize('update', $user);

        $oldValues = $this->auditValues($user);
        $managedUser = $managedUserService->update($user, $request->validated());

        $activityLogger->log(
            'it.user.updated',
            $request->user(),
            $managedUser,
            metadata: ['email' => $managedUser->email, 'role_id' => $managedUser->role_id],
            oldValues: $oldValues,
            newValues: $this->auditValues($managedUser),
            request: $request,
        );

        return redirect()
            ->route('it.users.show', $managedUser)
            ->with('status', 'User berhasil diperbarui.');
    }

    public function deactivate(
        Request $request,
        User $user,
        ManagedUserService $managedUserService,
        ActivityLogger $activityLogger,
    ): RedirectResponse {
        Gate::authorize('deactivate', $user);

        $oldValues = $user->only(['is_active']);
        $managedUser = $managedUserService->setActive($user, false);

        $activityLogger->log(
            'it.user.deactivated',
            $request->user(),
            $managedUser,
            metadata: ['email' => $managedUser->email],
            oldValues: $oldValues,
            newValues: ['is_active' => false],
            request: $request,
        );

        return redirect()
            ->route('it.users.show', $managedUser)
            ->with('status', 'User dinonaktifkan.');
    }

    public function reactivate(
        Request $request,
        User $user,
        ManagedUserService $managedUserService,
        ActivityLogger $activityLogger,
    ): RedirectResponse {
        Gate::authorize('reactivate', $user);

        $oldValues = $user->only(['is_active']);
        $managedUser = $managedUserService->setActive($user, true);

        $activityLogger->log(
            'it.user.reactivated',
            $request->user(),
            $managedUser,
            metadata: ['email' => $managedUser->email],
            oldValues: $oldValues,
            newValues: ['is_active' => true],
            request: $request,
        );

        return redirect()
            ->route('it.users.show', $managedUser)
            ->with('status', 'User diaktifkan kembali.');
    }

    /**
     * @param array<string, mixed> $extra
     * @return array<string, mixed>
     */
    private function formData(array $extra = []): array
    {
        return $extra + [
            'roles' => Role::query()->orderBy('scope_level')->orderBy('name')->get(),
            'holdings' => Holding::query()->orderBy('name')->get(),
            'regions' => HoldingCityPosition::query()->with('city')->orderBy('name')->get(),
            'brands' => Brand::query()->orderBy('name')->get(),
            'cities' => City::query()->orderBy('name')->get(),
            'branches' => Branch::query()->withoutGlobalScopes()->orderBy('name')->get(),
            'warehouses' => Warehouse::query()->withoutGlobalScopes()->orderBy('name')->get(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function auditValues(User $user): array
    {
        return $user->only([
            'name',
            'email',
            'role_id',
            'holding_id',
            'holding_city_position_id',
            'brand_id',
            'city_id',
            'branch_id',
            'warehouse_id',
            'is_active',
        ]);
    }
}
