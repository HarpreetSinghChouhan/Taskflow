<x-layouts.dashboard title="Edit Team Member">

    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <a href="{{ route('admin.users.index') }}" class="text-decoration-none text-muted fs-13 d-inline-flex align-items-center gap-1 mb-2">
                    <i class="bi bi-arrow-left"></i> Back to Team Directory
                </a>
                <h3 class="fw-bold text-navy mb-1" style="font-size: 22px; letter-spacing: -0.4px;">
                    Edit Member: {{ $user->name }}
                </h3>
                <p class="text-muted fs-13 mb-0">
                    Update profile, role designation, and portal permissions.
                </p>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <div class="d-flex gap-2">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-5 flex-shrink-0"></i>
                <div>
                    <strong class="d-block mb-1">Please fix the following errors:</strong>
                    <ul class="mb-0 ps-3 fs-13">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <x-card>
                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    {{-- Role Selection --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold text-navy fs-14 mb-2">
                            Assigned Role <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required {{ $user->id === Auth::id() ? 'disabled' : '' }}>
                            @if ($user->role === 'admin')
                                <option value="admin" selected>Administrator (System Owner)</option>
                            @else
                                <option value="project_manager" {{ old('role', $user->role) === 'project_manager' ? 'selected' : '' }}>
                                    Project Manager (PM)
                                </option>
                                <option value="team_leader" {{ old('role', $user->role) === 'team_leader' ? 'selected' : '' }}>
                                    Team Leader (TL)
                                </option>
                                <option value="employee" {{ old('role', $user->role) === 'employee' ? 'selected' : '' }}>
                                    Employee (Software Engineer / Developer)
                                </option>
                            @endif
                        </select>
                        @if ($user->id === Auth::id())
                            <input type="hidden" name="role" value="admin">
                            <small class="text-muted fs-12">You cannot modify your own administrative role.</small>
                        @endif
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold text-navy fs-13">
                                Full Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                                   value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold text-navy fs-13">
                                Email Address <span class="text-danger">*</span>
                            </label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="phone" class="form-label fw-semibold text-navy fs-13">
                                Phone Number
                            </label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone"
                                   value="{{ old('phone', $user->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="designation" class="form-label fw-semibold text-navy fs-13">
                                Designation / Job Title
                            </label>
                            <input type="text" class="form-control @error('designation') is-invalid @enderror" id="designation" name="designation"
                                   value="{{ old('designation', $user->designation) }}">
                            @error('designation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="joining_date" class="form-label fw-semibold text-navy fs-13">
                                Joining Date
                            </label>
                            <input type="date" class="form-control @error('joining_date') is-invalid @enderror" id="joining_date" name="joining_date"
                                   value="{{ old('joining_date', $user->joining_date ? $user->joining_date->format('Y-m-d') : '') }}">
                            @error('joining_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        

                        <div class="col-md-6">
                            <label for="status" class="form-label fw-semibold text-navy fs-13">
                                Account Status <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required {{ $user->id === Auth::id() ? 'disabled' : '' }}>
                                <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @if ($user->id === Auth::id())
                                <input type="hidden" name="status" value="active">
                            @endif
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- Reports To (shown only for Team Leader and Employee) --}}
                        <div class="row g-3 mb-4" id="reportsToWrap" style="display: none;">
                            <div class="col-md-6">
                                <label for="reports_to" class="form-label fw-semibold text-navy fs-13">
                                    <span id="reportsToLabel">Reports To</span> <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('reports_to') is-invalid @enderror" id="reports_to" name="reports_to">
                                    <option value="">Select...</option>
                                    @foreach ($projectManagers as $pm)
                                    <option value="{{ $pm->id }}" data-role="project_manager">{{ $pm->name }}</option>
                                    @endforeach
                                    @foreach ($teamLeaders as $tl)
                                    <option value="{{ $tl->id }}" data-role="team_leader">{{ $tl->name }}</option>
                                    @endforeach
                                </select>
                                <small id="noParentHint" class="text-danger fs-12 d-none">
                                    No active manager found. Create one first.
                                </small>
                                @error('reports_to')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="my-4" style="border-color: var(--dash-border);">

                    {{-- Password Reset (Optional) --}}
                    <div class="mb-4">
                        <label for="password" class="form-label fw-semibold text-navy fs-13">
                            Change Password <small class="text-muted fw-normal">(Leave blank to keep existing password)</small>
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password"
                                   placeholder="Enter new password if changing">
                            <button class="btn btn-outline-secondary" type="button" id="togglePasswordBtn">
                                <i class="bi bi-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Form Actions --}}
                    <div class="d-flex align-items-center gap-3 pt-2">
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm" style="background: var(--dash-brand); border: none;">
                            <i class="bi bi-check2 me-1"></i> Update Member
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3">
                            Cancel
                        </a>
                    </div>
                </form>
            </x-card>
        </div>

        <div class="col-lg-4 mt-4 mt-lg-0">
            <x-card title="Account Details" icon="bi-person-circle">
                <div class="text-center py-3">
                    @php
                        $roleColors = [
                            'admin' => '#1e293b',
                            'project_manager' => '#4f46e5',
                            'team_leader' => '#d97706',
                            'employee' => '#059669',
                        ];
                        $initials = collect(explode(' ', $user->name))->map(fn($part) => strtoupper(substr($part, 0, 1)))->take(2)->join('');
                    @endphp
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold shadow-sm mb-3"
                         style="width: 64px; height: 64px; background: {{ $roleColors[$user->role] ?? '#475569' }}; font-size: 20px;">
                        {{ $initials }}
                    </div>
                    <h6 class="fw-bold text-navy mb-0">{{ $user->name }}</h6>
                    <small class="text-muted">{{ $user->email }}</small>
                </div>
                <ul class="list-group list-group-flush fs-13 border-top">
                    <li class="list-group-item d-flex justify-content-between px-0 py-2">
                        <span class="text-muted">Account ID:</span>
                        <span class="fw-semibold text-navy">#{{ $user->id }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0 py-2">
                        <span class="text-muted">Current Role:</span>
                        <span class="fw-semibold text-capitalize">{{ str_replace('_', ' ', $user->role) }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0 py-2">
                        <span class="text-muted">Registered On:</span>
                        <span>{{ $user->created_at ? $user->created_at->format('M d, Y') : '—' }}</span>
                    </li>
                </ul>
            </x-card>
        </div>
    </div>

    <x-slot:scripts>
        <script>
            $(document).ready(function() {
                $('#togglePasswordBtn').on('click', function() {
                    const pwd = $('#password');
                    const icon = $('#toggleIcon');
                    if (pwd.attr('type') === 'password') {
                        pwd.attr('type', 'text');
                        icon.removeClass('bi-eye').addClass('bi-eye-slash');
                    } else {
                        pwd.attr('type', 'password');
                        icon.removeClass('bi-eye-slash').addClass('bi-eye');
                    }
                });
                // Keep all manager options, then show only the ones for the chosen role
                const allParentOptions = $('#reports_to option[data-role]').clone();
                const savedParent = @json(old('reports_to', $user->reports_to));

                function refreshReportsTo() {
                    const role = $('#role').val();
                    const parentRole = role === 'team_leader' ? 'project_manager'
                     : role === 'employee' ? 'team_leader' : null;
                    const $select = $('#reports_to');

                    if (!parentRole) {
                        $('#reportsToWrap').hide();
                        $select.prop('disabled', true).val('');
                        return;
                    }

                    $('#reportsToWrap').show();
                    $select.prop('disabled', false);
                    $('#reportsToLabel').text(parentRole === 'project_manager'
                    ? 'Reports To (Project Manager)' : 'Reports To (Team Leader)');

                    $select.find('option[data-role]').remove();
                    const matches = allParentOptions.filter('[data-role="' + parentRole + '"]').clone();
                    $select.append(matches);

                    $select.val(savedParent);
                    if (!$select.val()) $select.val('');
                        $('#noParentHint').toggleClass('d-none', matches.length > 0);
                }

                $('#role').on('change', refreshReportsTo);
                refreshReportsTo();
            });
        </script>
    </x-slot:scripts>

</x-layouts.dashboard>
