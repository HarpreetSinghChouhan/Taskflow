<x-layouts.dashboard title="Create Team Member">

    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <a href="{{ route('admin.users.index') }}" class="text-decoration-none text-muted fs-13 d-inline-flex align-items-center gap-1 mb-2">
                    <i class="bi bi-arrow-left"></i> Back to Team Directory
                </a>
                <h3 class="fw-bold text-navy mb-1" style="font-size: 22px; letter-spacing: -0.4px;">
                    Create New Team Member
                </h3>
                <p class="text-muted fs-13 mb-0">
                    Add a new Project Manager, Team Leader, or Employee to the workspace.
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
        <div class="col-lg-12">
            <x-card>
                <form method="POST" action="{{ route('admin.users.store') }}" id="createUserForm">
                    @csrf

                    {{-- Step 1: Select Role --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold text-navy fs-14 mb-2">
                            Select Account Role <span class="text-danger">*</span>
                        </label>
                        <div class="row g-3">
                            {{-- Project Manager Option --}}
                            <div class="col-md-4">
                                <label class="role-selector-card w-100 p-3 rounded-3 border cursor-pointer h-100 d-block position-relative"
                                       style="transition: all 0.2s ease;" id="label-role-pm">
                                    <input type="radio" name="role" value="project_manager" class="role-radio visually-hidden"
                                           {{ old('role', $defaultRole) === 'project_manager' ? 'checked' : '' }} required>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="rounded-2 d-flex align-items-center justify-content-center"
                                             style="width: 38px; height: 38px; background: #eef2ff; color: #4f46e5; font-size: 18px;">
                                            <i class="bi bi-briefcase-fill"></i>
                                        </div>
                                        <i class="bi bi-check-circle-fill fs-5 text-indigo check-icon d-none"></i>
                                    </div>
                                    <div class="fw-bold text-navy fs-14 mb-1">Project Manager</div>
                                    <small class="text-muted fs-12 d-block">Manages client projects, sprints, and delivery pipelines.</small>
                                </label>
                            </div>

                            {{-- Team Leader Option --}}
                            <div class="col-md-4">
                                <label class="role-selector-card w-100 p-3 rounded-3 border cursor-pointer h-100 d-block position-relative"
                                       style="transition: all 0.2s ease;" id="label-role-tl">
                                    <input type="radio" name="role" value="team_leader" class="role-radio visually-hidden"
                                           {{ old('role', $defaultRole) === 'team_leader' ? 'checked' : '' }} required>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="rounded-2 d-flex align-items-center justify-content-center"
                                             style="width: 38px; height: 38px; background: #fffbeb; color: #d97706; font-size: 18px;">
                                            <i class="bi bi-diagram-3-fill"></i>
                                        </div>
                                        <i class="bi bi-check-circle-fill fs-5 text-warning check-icon d-none"></i>
                                    </div>
                                    <div class="fw-bold text-navy fs-14 mb-1">Team Leader</div>
                                    <small class="text-muted fs-12 d-block">Leads sub-teams, reviews code, and assigns tickets.</small>
                                </label>
                            </div>

                            {{-- Employee Option --}}
                            <div class="col-md-4">
                                <label class="role-selector-card w-100 p-3 rounded-3 border cursor-pointer h-100 d-block position-relative"
                                       style="transition: all 0.2s ease;" id="label-role-emp">
                                    <input type="radio" name="role" value="employee" class="role-radio visually-hidden"
                                           {{ old('role', $defaultRole) === 'employee' ? 'checked' : '' }} required>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="rounded-2 d-flex align-items-center justify-content-center"
                                             style="width: 38px; height: 38px; background: #ecfdf5; color: #059669; font-size: 18px;">
                                            <i class="bi bi-person-badge-fill"></i>
                                        </div>
                                        <i class="bi bi-check-circle-fill fs-5 text-success check-icon d-none"></i>
                                    </div>
                                    <div class="fw-bold text-navy fs-14 mb-1">Employee</div>
                                    <small class="text-muted fs-12 d-block">Executes tasks, submits daily sprint logs, and codes.</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4" style="border-color: var(--dash-border);">

                    {{-- Step 2: Personal & Contact Information --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold text-navy fs-13">
                                Full Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                                   placeholder="e.g. John Doe" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold text-navy fs-13">
                                Email Address <span class="text-danger">*</span>
                            </label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                                   placeholder="e.g. john@baselineitdevelopment.com" value="{{ old('email') }}" required>
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
                                   placeholder="e.g. +91 98765 43210" value="{{ old('phone') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="designation" class="form-label fw-semibold text-navy fs-13">
                                Designation / Job Title
                            </label>
                            <input type="text" class="form-control @error('designation') is-invalid @enderror" id="designation" name="designation"
                                   placeholder="e.g. Senior Project Manager / Backend Dev" value="{{ old('designation') }}">
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
                                   value="{{ old('joining_date', date('Y-m-d')) }}">
                            @error('joining_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                       
                        <div class="col-md-6">
                            <label for="status" class="form-label fw-semibold text-navy fs-13">
                                Account Status <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active (Can login immediately)</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive (Access disabled)</option>
                            </select>
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
                                    <option value="{{ $pm->id }}" data-role="project_manager"
                                        {{ (string) old('reports_to') === (string) $pm->id ? 'selected' : '' }}>
                                        {{ $pm->name }}
                                    </option>
                                    @endforeach
                                    @foreach ($teamLeaders as $tl)
                                    <option value="{{ $tl->id }}" data-role="team_leader"
                                        {{ (string) old('reports_to') === (string) $tl->id ? 'selected' : '' }}>
                                        {{ $tl->name }}
                                    </option>
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

                    {{-- Step 3: Login Security & Password --}}
                    <div class="mb-4">
                        <label for="password" class="form-label fw-semibold text-navy fs-13">
                            Initial Login Password <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password"
                                   placeholder="Enter initial password (min 6 characters)" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePasswordBtn">
                                <i class="bi bi-eye" id="toggleIcon"></i>
                            </button>
                            <button class="btn btn-outline-primary" type="button" id="generatePasswordBtn">
                                <i class="bi bi-key-fill me-1"></i> Auto-Generate
                            </button>
                        </div>
                        <small class="text-muted fs-12 d-block">
                            Min 6 characters with uppercase, lowercase, number and a special character (@#$%^&*!~?&lt;&gt;).
                        </small>
                        @error('password')
                            <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Form Submission Actions --}}
                    <div class="d-flex align-items-center gap-3 pt-2">
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm" style="background: var(--dash-brand); border: none;">
                            <i class="bi bi-check2-circle me-1"></i> Create Member &amp; Activate
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3">
                            Cancel
                        </a>
                    </div>
                </form>
            </x-card>
        </div>

    </div>

    <x-slot:styles>
        <style>
            .role-selector-card {
                background: #ffffff;
                border-color: var(--dash-border) !important;
                transition: all 0.2s ease-in-out;
            }
            .role-selector-card:hover {
                border-color: #94a3b8 !important;
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            }
            .role-selector-card.selected-pm {
                border-color: #4f46e5 !important;
                background-color: #f5f3ff !important;
            }
            .role-selector-card.selected-tl {
                border-color: #d97706 !important;
                background-color: #fffbeb !important;
            }
            .role-selector-card.selected-emp {
                border-color: #059669 !important;
                background-color: #ecfdf5 !important;
            }
            .cursor-pointer {
                cursor: pointer;
            }
        </style>
    </x-slot:styles>

    <x-slot:scripts>
        <script>
            $(document).ready(function() {
                // Highlight active role card based on radio selection
                function refreshRoleCards() {
                    const selectedRole = $('input[name="role"]:checked').val();
                    $('.role-selector-card').removeClass('selected-pm selected-tl selected-emp');
                    $('.check-icon').addClass('d-none');

                    if (selectedRole === 'project_manager') {
                        $('#label-role-pm').addClass('selected-pm');
                        $('#label-role-pm .check-icon').removeClass('d-none');
                    } else if (selectedRole === 'team_leader') {
                        $('#label-role-tl').addClass('selected-tl');
                        $('#label-role-tl .check-icon').removeClass('d-none');
                    } else if (selectedRole === 'employee') {
                        $('#label-role-emp').addClass('selected-emp');
                        $('#label-role-emp .check-icon').removeClass('d-none');
                    }
                }

               // Keep all manager options, then show only the ones for the chosen role
                const allParentOptions = $('#reports_to option[data-role]').clone();
                const oldParent = @json(old('reports_to'));

                function refreshReportsTo() {
                    const role = $('input[name="role"]:checked').val();
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

                    $select.val(oldParent);
                    if (!$select.val()) $select.val('');
                    $('#noParentHint').toggleClass('d-none', matches.length > 0);
                }

                $('input[name="role"]').on('change', function () {
                    refreshRoleCards();
                    refreshReportsTo();
                }); 
                refreshRoleCards();
                refreshReportsTo();
                // Toggle Password Visibility
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

                // Auto-Generate Random Password
                // Auto-generate a password that always passes the rule
                $('#generatePasswordBtn').on('click', function() {
                    const upper = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
                    const lower = 'abcdefghjkmnpqrstuvwxyz';
                    const digits = '23456789';
                    const special = '@#$%^&*!~?<>';
                    const all = upper + lower + digits + special;

                    const rnd = (n) => crypto.getRandomValues(new Uint32Array(1))[0] % n;
                    const pick = (s) => s[rnd(s.length)];

                    // One of each type, then fill up to 10
                    const chars = [pick(upper), pick(lower), pick(digits), pick(special)];
                    while (chars.length < 10) chars.push(pick(all));

                    // Shuffle
                    for (let i = chars.length - 1; i > 0; i--) {
                        const j = rnd(i + 1);
                        [chars[i], chars[j]] = [chars[j], chars[i]];
                    }

                    $('#password').val(chars.join('')).attr('type', 'text');
                    $('#toggleIcon').removeClass('bi-eye').addClass('bi-eye-slash');
                });
            });
        </script>
    </x-slot:scripts>

</x-layouts.dashboard>
