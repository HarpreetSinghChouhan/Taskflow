<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Mail\WelcomeUserMail;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    /**
     * Display a listing of team members (PM, TL, Employees).
     */
    public function index(Request $request): View
    {
        $roleFilter = $request->query('role');
        $statusFilter = $request->query('status');
        $search = $request->query('search');

        // Base query
        $query = User::query();

        // Search by name, email, phone, or designation
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('designation', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($roleFilter && in_array($roleFilter, ['project_manager', 'team_leader', 'employee', 'admin'])) {
            $query->where('role', $roleFilter);
        }

        // Filter by status
        if ($statusFilter && in_array($statusFilter, ['active', 'inactive'])) {
            $query->where('status', $statusFilter);
        }

        $users = $query->with('manager')->latest('id')->paginate(12)->withQueryString();
        // Counts for tabs & stat badges
        $counts = [
            'all'             => User::count(),
            'project_manager' => User::where('role', 'project_manager')->count(),
            'team_leader'     => User::where('role', 'team_leader')->count(),
            'employee'        => User::where('role', 'employee')->count(),
            'active'          => User::where('status', 'active')->count(),
            'inactive'        => User::where('status', 'inactive')->count(),
        ];

        return view('admin.users.index', compact('users', 'counts', 'roleFilter', 'statusFilter', 'search'));
    }

    /**
     * Show the form for creating a new user (PM, TL, or Employee).
     */
    public function create(Request $request): View
    {
        // Support pre-selected role (e.g. ?role=project_manager)
        $defaultRole = $request->query('role', 'employee');
        if (!in_array($defaultRole, ['project_manager', 'team_leader', 'employee'])) {
            $defaultRole = 'employee';
        }
        return view('admin.users.create', array_merge(compact('defaultRole'), $this->managerOptions()));
    }

    /**
     * Store a newly created PM, TL, or Employee in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $parentRole = User::parentRoleFor((string) $request->input('role'));

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:100'],
            'email'        => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'phone'        => ['nullable', 'string', 'max:20'],
            'role'         => ['required', 'in:project_manager,team_leader,employee'],
            'designation'  => ['nullable', 'string', 'max:100'],
            'joining_date' => ['nullable', 'date'],
            'password'     => $this->passwordRules(true),
            'status'       => ['required', 'in:active,inactive'],
            'reports_to'   => $this->reportsToRules($parentRole),
        ], [
            'role.in' => 'Please select a valid role (Project Manager, Team Leader, or Employee).',
            'email.unique' => 'A user with this email address already exists.',
            'password.min' => 'Password must be at least 6 characters.',
            'password.regex' => 'Password needs 1 uppercase, 1 lowercase, 1 number and 1 special character (@#$%^&*!~?<>).',
            'reports_to.required' => 'Please select who this member reports to.',
            'reports_to.exists'   => 'Selected manager is invalid or inactive.',
        ]);

        $user = User::create([
            'name'         => $validated['name'],
            'email'        => $validated['email'],
            'phone'        => $validated['phone'] ?? null,
            'role'         => $validated['role'],
            'designation'  => $validated['designation'] ?? null,
            'joining_date' => $validated['joining_date'] ?? now()->toDateString(),
            'password'     => $validated['password'],
            'status'       => $validated['status'] ?? 'active',
            'reports_to'   => $parentRole ? $validated['reports_to'] : null,
        ]);

        $roleLabels = [
            'project_manager' => 'Project Manager',
            'team_leader'     => 'Team Leader',
            'employee'        => 'Employee',
        ];
        $roleName = $roleLabels[$user->role] ?? 'User';

        // Send welcome email; do not block creation if mail fails
        try {
            Mail::to($user->email)->send(new WelcomeUserMail($user, $validated['password']));
            $mailNote = 'Welcome email sent.';
        } catch (\Throwable $e) {
            report($e);
            $mailNote = 'But the welcome email could not be sent.';
        }

        return redirect()
            ->route('admin.users.index', ['role' => $user->role])
            ->with('success', "{$roleName} '{$user->name}' created successfully! {$mailNote}");
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
       return view('admin.users.edit', array_merge(compact('user'), $this->managerOptions()));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $parentRole = User::parentRoleFor((string) $request->input('role'));

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:100'],
            'email'        => ['required', 'string', 'email', 'max:150', Rule::unique('users')->ignore($user->id)],
            'phone'        => ['nullable', 'string', 'max:20'],
            'role'         => ['required', 'in:admin,project_manager,team_leader,employee'],
            'designation'  => ['nullable', 'string', 'max:100'],
            'joining_date' => ['nullable', 'date'],
            'password'     => $this->passwordRules(false),
            'status'       => ['required', 'in:active,inactive'],
            'reports_to'   => $this->reportsToRules($parentRole),
        ], [
            'password.min' => 'Password must be at least 6 characters.',
            'password.regex' => 'Password needs 1 uppercase, 1 lowercase, 1 number and 1 special character (@#$%^&*!~?<>).',
            'reports_to.required' => 'Please select who this member reports to.',
            'reports_to.exists'   => 'Selected manager is invalid or inactive.',
        ]);

        // Block role change if this user has people reporting to them
        if ($validated['role'] !== $user->role && $user->reports()->exists()) {
            return back()->withInput()->withErrors([
                'role' => 'This member has people reporting to them. Reassign them before changing the role.',
            ]);
        }

        $user->name         = $validated['name'];
        $user->email        = $validated['email'];
        $user->phone        = $validated['phone'] ?? null;
        $user->role         = $validated['role'];
        $user->designation  = $validated['designation'] ?? null;
        $user->joining_date = $validated['joining_date'] ?? $user->joining_date;
        $user->status       = $validated['status'];
        $user->reports_to   = $parentRole ? $validated['reports_to'] : null;

        if (!empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()
            ->route('admin.users.index', ['role' => $user->role])
            ->with('success', "User '{$user->name}' updated successfully.");
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot change your own account status.');
        }

        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        return back()->with('success', "Status of '{$user->name}' changed to " . ucfirst($user->status) . '.');
    }

    /**
     * Remove the specified user from storage (Soft Delete).
     */
   public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own admin account.');
        }

        // Block delete if people still report to this user
        $reportCount = $user->reports()->count();
        if ($reportCount > 0) {
            return back()->with('error', "'{$user->name}' has {$reportCount} member(s) reporting to them. Reassign them first.");
        }

        $userName = $user->name;
        $user->delete();

        return back()->with('success', "User '{$userName}' has been deleted successfully.");
    }
    // Active managers shown in the "Reports To" dropdown
    private function managerOptions(): array
    {   
        return [
            'projectManagers' => User::where('role', 'project_manager')
                ->where('status', 'active')->orderBy('name')->get(['id', 'name']),
            'teamLeaders' => User::where('role', 'team_leader')
                ->where('status', 'active')->orderBy('name')->get(['id', 'name']),
        ];
    }

    // Validation rules for reports_to based on the parent role
    private function reportsToRules(?string $parentRole): array
    {
        if (!$parentRole) {
            return ['nullable'];
        }
        return [
            'required',
            Rule::exists('users', 'id')->where(
                fn ($q) => $q->where('role', $parentRole)
                ->where('status', 'active')
                ->whereNull('deleted_at')
            ),
        ];
    }
    // Min 6 chars with upper, lower, number and a special char
private function passwordRules(bool $required): array
{
    return [
        $required ? 'required' : 'nullable',
        'string',
        'min:6',
        'regex:/[a-z]/',
        'regex:/[A-Z]/',
        'regex:/[0-9]/',
        'regex:/[@#$%^&*!~?<>]/',
    ];
}
}
