<x-app-layout title="Compliance Requests - CSU Lal-lo Admin">

    <div x-data="{ 
        requestModalOpen: {{ $errors->any() ? 'true' : 'false' }},
        requirements: [
            { name: 'Certificate of Enrollment (COE)', instruction: 'Official COE for the current semester stamped by the University Registrar.', is_required: true },
            { name: 'Photocopy of Valid School ID', instruction: 'Clear copy showing both front and back with current validation sticker.', is_required: true },
            { name: 'Certificate of Grades (COG) / Summary of Grades', instruction: 'Authenticated copy of grades from the preceding semester.', is_required: true }
        ],
        addRequirement() {
            this.requirements.push({ name: '', instruction: '', is_required: true });
        },
        removeRequirement(index) {
            if (this.requirements.length > 1) {
                this.requirements.splice(index, 1);
            }
        }
    }">

        <!-- Header Section -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Compliance Requests</h1>
                <p class="text-xs text-slate-500 font-medium mt-1">
                    Request and monitor requirements (e.g. COE, ID, COG) from active scholars without requiring new scholarship applications.
                </p>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button @click="requestModalOpen = true" 
                        type="button"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#3B060F] text-white font-extrabold text-xs rounded-xl hover:bg-[#6B0F1A] transition shadow-md cursor-pointer border border-[#FFC107]/30">
                    <svg class="w-4 h-4 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Request Compliance
                </button>
            </div>
        </div>

        <!-- Stats Overview Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <!-- Total Requests -->
            <div class="p-4 rounded-xl border bg-white border-slate-200 shadow-xs flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Requests</span>
                <span class="text-2xl font-extrabold text-slate-900 mt-1">{{ number_format($stats['total_requests']) }}</span>
            </div>

            <!-- Active Requests -->
            <div class="p-4 rounded-xl border bg-emerald-50 border-emerald-200 shadow-xs flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-800">Active Requests</span>
                <span class="text-2xl font-extrabold text-emerald-700 mt-1">{{ number_format($stats['active_requests']) }}</span>
            </div>

            <!-- Total Assigned Scholars -->
            <div class="p-4 rounded-xl border bg-blue-50 border-blue-200 shadow-xs flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-800">Assigned Scholars</span>
                <span class="text-2xl font-extrabold text-blue-700 mt-1">{{ number_format($stats['assigned_scholars']) }}</span>
            </div>

            <!-- Pending Document Reviews -->
            <div class="p-4 rounded-xl border bg-amber-50 border-amber-200 shadow-xs flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-800">Pending Reviews</span>
                <span class="text-2xl font-extrabold text-amber-700 mt-1">{{ number_format($stats['pending_reviews']) }}</span>
            </div>

            <!-- Completed Compliances -->
            <div class="p-4 rounded-xl border bg-[#3B060F] border-[#3B060F] shadow-xs flex flex-col justify-between text-white">
                <span class="text-[11px] font-bold uppercase tracking-wider text-[#FFC107]">Completed</span>
                <span class="text-2xl font-extrabold mt-1">{{ number_format($stats['completed']) }}</span>
            </div>
        </div>

        <!-- Filter & Search Form -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-4 mb-6">
            <form method="GET" action="{{ route('admin.compliance.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                <!-- Search Input -->
                <div class="sm:col-span-5 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}" 
                           placeholder="Search request title, semester, or scholarship..." 
                           {{ $search ? 'autofocus onfocus="this.setSelectionRange(this.value.length, this.value.length)"' : '' }}
                           oninput="clearTimeout(window._searchTimer); window._searchTimer = setTimeout(() => this.form.submit(), 400)"
                           class="w-full py-2 pl-9 pr-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                </div>

                <!-- Scholarship Filter -->
                <div class="sm:col-span-4">
                    <select name="scholarship_id" onchange="this.form.submit()" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 font-semibold focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                        <option value="">All Scholarship Programs</option>
                        @foreach($scholarships as $sch)
                            <option value="{{ $sch->id }}" {{ $scholarshipId == $sch->id ? 'selected' : '' }}>
                                {{ $sch->school_year_label }} - {{ $sch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="sm:col-span-3 flex items-center gap-2">
                    <select name="status" onchange="this.form.submit()" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 font-semibold focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                        <option value="">All Statuses</option>
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="extended" {{ $status === 'extended' ? 'selected' : '' }}>Extended</option>
                        <option value="closed" {{ $status === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>

                    @if($search || $scholarshipId || $status || $schoolYear)
                        <a href="{{ route('admin.compliance.index') }}" class="px-2.5 py-1.5 text-xs font-bold text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition shrink-0" title="Reset Filters">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Compliance Requests Table -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#3B060F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Compliance Requests Batch Log
                </h3>
                <span class="text-xs font-semibold text-slate-500">
                    Showing {{ $complianceRequests->firstItem() ?? 0 }} to {{ $complianceRequests->lastItem() ?? 0 }} of {{ $complianceRequests->total() }} Requests
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-100/70 text-[11px] font-bold text-slate-500 uppercase border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3">Program & Request Title</th>
                            <th class="px-6 py-3">Period</th>
                            <th class="px-6 py-3">Requirements</th>
                            <th class="px-6 py-3">Assigned Scholars</th>
                            <th class="px-6 py-3">Deadline</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($complianceRequests as $req)
                            <tr class="hover:bg-slate-50/80 transition">
                                <!-- Program & Request Title -->
                                <td class="px-6 py-4">
                                    <div class="font-extrabold text-slate-900 text-sm">
                                        {{ $req->title }}
                                    </div>
                                    <div class="text-[11px] text-slate-500 font-semibold mt-0.5 flex items-center gap-1.5">
                                        <span class="text-[#3B060F]">{{ $req->scholarship->name }}</span>
                                        <span class="text-slate-300">•</span>
                                        <span class="{{ $req->scholarship->isContinuing() ? 'text-emerald-700' : 'text-blue-700' }}">{{ $req->scholarship->coverage_type_label }}</span>
                                    </div>
                                </td>

                                <!-- Period -->
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800 text-xs">
                                        {{ $req->semester }}
                                    </div>
                                    <div class="text-[11px] text-slate-500 font-medium">
                                        AY {{ $req->school_year }}
                                    </div>
                                </td>

                                <!-- Requirements list count -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 font-bold text-slate-700 text-[11px] border border-slate-200">
                                            {{ $req->requirements->count() }} items
                                        </span>
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-1 truncate max-w-[180px]" title="{{ $req->requirements->pluck('name')->implode(', ') }}">
                                        {{ $req->requirements->pluck('name')->implode(', ') }}
                                    </div>
                                </td>

                                <!-- Assigned Scholars & Progress -->
                                <td class="px-6 py-4">
                                    @php
                                        $totalAssigned = $req->scholarCompliances->count();
                                        $completedCount = $req->scholarCompliances->where('status', 'completed')->count();
                                        $percent = $totalAssigned > 0 ? round(($completedCount / $totalAssigned) * 100) : 0;
                                    @endphp
                                    <div class="flex items-center justify-between text-[11px] font-bold text-slate-700 mb-1">
                                        <span>{{ $completedCount }} / {{ $totalAssigned }} Complete</span>
                                        <span>{{ $percent }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-emerald-600 h-1.5 rounded-full" style="width: {{ $percent }}%"></div>
                                    </div>
                                </td>

                                <!-- Deadline -->
                                <td class="px-6 py-4">
                                    <div class="font-bold {{ $req->isPastDeadline() ? 'text-red-700' : 'text-slate-800' }}">
                                        {{ $req->deadline->format('M d, Y') }}
                                    </div>
                                    <div class="text-[10px] mt-0.5">
                                        @if($req->isPastDeadline())
                                            <span class="text-red-600 font-bold">Past Deadline</span>
                                        @else
                                            <span class="text-slate-400 font-medium">{{ $req->days_remaining }} left</span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                                        @if($req->status === 'active') bg-emerald-100 text-emerald-800 border border-emerald-300
                                        @elseif($req->status === 'extended') bg-blue-100 text-blue-800 border border-blue-300
                                        @else bg-slate-100 text-slate-800 border border-slate-300 @endif">
                                        {{ ucfirst($req->status) }}
                                    </span>
                                </td>

                                <!-- Action Buttons -->
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.compliance.show', $req->id) }}" 
                                           class="inline-flex items-center px-3 py-1.5 bg-[#3B060F] text-white font-bold rounded-lg text-xs hover:bg-[#6B0F1A] transition shadow-xs cursor-pointer shrink-0">
                                            Monitor Roster
                                            <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>

                                        <form method="POST" action="{{ route('admin.compliance.destroy', $req->id) }}" onsubmit="return confirm('Are you sure you want to delete this compliance request? All submission records will be permanently removed.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition" title="Delete Request">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-500 text-xs">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                        </svg>
                                        <h3 class="text-sm font-extrabold text-slate-800">No Compliance Requests Created Yet</h3>
                                        <p class="text-xs text-slate-400 mt-1 max-w-md">Click the <strong>Request Compliance</strong> button above to request periodic compliance documents (e.g. COE, Valid School ID, COG) from active scholars.</p>
                                        <button @click="requestModalOpen = true" type="button" class="mt-4 px-4 py-2 bg-[#3B060F] text-white text-xs font-bold rounded-xl hover:bg-[#6B0F1A] transition shadow-xs">
                                            + Create First Compliance Request
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($complianceRequests->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                    {{ $complianceRequests->links() }}
                </div>
            @endif
        </div>

        <!-- ========================================== -->
        <!-- CREATE COMPLIANCE REQUEST MODAL -->
        <!-- ========================================== -->
        <div x-show="requestModalOpen" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             style="display: none;" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">

            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="requestModalOpen = false"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full p-6 text-left overflow-hidden border border-slate-200"
                     @click.away="requestModalOpen = false">

                    <!-- Modal Header -->
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center space-x-3">
                            <div class="p-2.5 bg-emerald-50 text-[#3B060F] rounded-xl border border-emerald-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900">Request Compliance from Active Scholars</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Collect semester periodic requirements from existing grantees without altering their scholarship standing.</p>
                            </div>
                        </div>
                        <button @click="requestModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body Form -->
                    <form method="POST" action="{{ route('admin.compliance.store') }}" class="mt-4 space-y-4">
                        @csrf

                        <!-- Explanatory Notice Banner -->
                        <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-emerald-700 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="leading-relaxed">
                                <strong>Automatic Scholar Dispatch:</strong> Upon publishing, this compliance request will be automatically assigned to <strong>all currently active scholars</strong> enrolled under the selected scholarship program. Notifications will be dispatched immediately.
                            </div>
                        </div>

                        <!-- Scholarship Program -->
                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">
                                Scholarship Program <span class="text-red-500">*</span>
                            </label>
                            <select name="scholarship_id" required class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                                <option value="">Select Scholarship Program...</option>
                                @foreach($scholarships as $sch)
                                    <option value="{{ $sch->id }}" {{ old('scholarship_id') == $sch->id ? 'selected' : '' }}>
                                        {{ $sch->school_year_label }} - {{ $sch->name }} ({{ $sch->coverage_type_label }})
                                    </option>
                                @endforeach
                            </select>
                            @error('scholarship_id') <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- School Year & Semester Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">
                                    School Year <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="school_year" 
                                       placeholder="e.g. 2026-2027" 
                                       value="{{ old('school_year', date('Y') . '-' . (date('Y')+1)) }}" 
                                       required
                                       class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                                @error('school_year') <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">
                                    Semester / Term <span class="text-red-500">*</span>
                                </label>
                                <select name="semester" required class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                                    <option value="1st Semester" {{ old('semester') === '1st Semester' ? 'selected' : '' }}>1st Semester</option>
                                    <option value="2nd Semester" {{ old('semester') === '2nd Semester' ? 'selected' : '' }}>2nd Semester</option>
                                    <option value="Summer Term" {{ old('semester') === 'Summer Term' ? 'selected' : '' }}>Summer Term</option>
                                </select>
                                @error('semester') <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Submission Deadline & Custom Title -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">
                                    Submission Deadline <span class="text-red-500">*</span>
                                </label>
                                <input type="date" 
                                       name="deadline" 
                                       min="{{ date('Y-m-d') }}"
                                       value="{{ old('deadline', date('Y-m-d', strtotime('+14 days'))) }}" 
                                       required
                                       class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                                @error('deadline') <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">
                                    Request Title <span class="text-slate-400 font-normal">(Optional)</span>
                                </label>
                                <input type="text" 
                                       name="title" 
                                       placeholder="Leave blank to auto-generate" 
                                       value="{{ old('title') }}" 
                                       class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                                @error('title') <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Instructions -->
                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">
                                General Guidelines & Instructions <span class="text-slate-400 font-normal">(Optional)</span>
                            </label>
                            <textarea name="instructions" 
                                      rows="2" 
                                      placeholder="Provide notes or reminders for scholars regarding file formatting or submission rules..." 
                                      class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">{{ old('instructions') }}</textarea>
                            @error('instructions') <p class="text-red-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Requirements Checklist Builder -->
                        <div class="pt-2 border-t border-slate-100">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider">
                                    Required Compliance Documents <span class="text-red-500">*</span>
                                </label>
                                <button type="button" 
                                        @click="addRequirement()" 
                                        class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 hover:text-emerald-800 hover:underline">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add Requirement Item
                                </button>
                            </div>

                            <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                                <template x-for="(req, index) in requirements" :key="index">
                                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 flex items-start gap-2">
                                        <div class="flex-1 space-y-1.5">
                                            <input type="text" 
                                                   :name="'requirements[' + index + '][name]'" 
                                                   x-model="req.name" 
                                                   placeholder="Document Title (e.g. Certificate of Enrollment)" 
                                                   required
                                                   class="w-full py-1.5 px-2.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                                            <input type="text" 
                                                   :name="'requirements[' + index + '][instruction]'" 
                                                   x-model="req.instruction" 
                                                   placeholder="Specific instructions / remarks (optional)" 
                                                   class="w-full py-1 px-2.5 bg-white border border-slate-200 rounded-lg text-[11px] text-slate-600 placeholder-slate-400 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                                        </div>
                                        <button type="button" 
                                                @click="removeRequirement(index)" 
                                                x-show="requirements.length > 1"
                                                class="p-1.5 text-slate-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition shrink-0" 
                                                title="Remove Item">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                            <button type="button" 
                                    @click="requestModalOpen = false" 
                                    class="px-4 py-2 bg-slate-100 text-slate-700 font-bold text-xs rounded-xl hover:bg-slate-200 transition">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-5 py-2 bg-[#3B060F] text-white font-extrabold text-xs rounded-xl hover:bg-[#6B0F1A] transition shadow-md">
                                Dispatch Compliance Request
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>

</x-app-layout>
