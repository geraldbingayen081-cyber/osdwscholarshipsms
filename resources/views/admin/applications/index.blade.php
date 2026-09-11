<script>
    function applicationsManager(statsData, appIds) {
        return {
            stats: statsData,
            selectedIds: [],
            allAppIds: appIds,
            toast: { show: false, message: '', scholarUrl: '' },
            toggleAll(event) {
                if (event.target.checked) {
                    this.selectedIds = [...this.allAppIds];
                } else {
                    this.selectedIds = [];
                }
            },
            async approveApp(appId, studentName, csrfToken) {
                if (!confirm('Are you sure you want to approve the application for ' + studentName + '? This will automatically enroll them in Active Scholars.')) {
                    return;
                }
                try {
                    const response = await fetch('/admin/applications/' + appId + '/status', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ status: 'approved' })
                    });
                    const data = await response.json();
                    if (data.success) {
                        const row = document.getElementById('app-row-' + appId);
                        if (row) {
                            row.style.transition = 'all 0.4s ease-out';
                            row.style.opacity = '0';
                            row.style.transform = 'translateX(20px)';
                            setTimeout(() => {
                                row.remove();
                                const tbody = document.getElementById('applications-tbody');
                                if (tbody && tbody.querySelectorAll('tr[id^="app-row-"]').length === 0) {
                                    const emptyRow = document.getElementById('empty-state-row');
                                    if (emptyRow) emptyRow.classList.remove('hidden');
                                }
                            }, 400);
                        }
                        if (data.stats) {
                            this.stats = data.stats;
                        }
                        this.toast.message = data.message;
                        this.toast.scholarUrl = data.scholar_url;
                        this.toast.show = true;
                        setTimeout(() => { this.toast.show = false; }, 7000);
                    } else {
                        alert(data.message || 'Failed to approve application.');
                    }
                } catch (e) {
                    console.error(e);
                    alert('An error occurred while approving the application.');
                }
            }
        };
    }
</script>

<x-app-layout title="Manage Applications - CSU Lal-lo Admin">

    <div x-data="applicationsManager({{ json_encode($stats) }}, {{ json_encode($applications->pluck('id')->map(fn($id) => (string)$id)->values()->all()) }})">

        <!-- Header Section -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Student Applications</h1>
                <p class="text-xs text-slate-500 font-medium mt-1">
                    Review, verify documents, and approve or reject scholarship applications for CSU Lal-lo Campus.
                </p>
            </div>

            <a href="{{ route('admin.export.applications', request()->all()) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-[#3B060F] text-white font-extrabold text-xs rounded-xl hover:bg-[#6B0F1A] transition shadow-md shrink-0 cursor-pointer">
                <svg class="w-4 h-4 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export Applications (CSV)
            </a>
        </div>

        <!-- Metric Tabs Row -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
            <!-- All Applications -->
            <a href="{{ route('admin.applications.index') }}" 
               class="p-4 rounded-xl border transition flex flex-col justify-between {{ empty($status) ? 'bg-[#3B060F] text-white border-[#3B060F] shadow-sm' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-300' }}">
                <span class="text-[11px] font-bold uppercase tracking-wider {{ empty($status) ? 'text-[#FFC107]' : 'text-slate-500' }}">All Apps</span>
                <span class="text-2xl font-extrabold mt-1" x-text="stats.total">{{ number_format($stats['total']) }}</span>
            </a>

            <!-- Submitted -->
            <a href="{{ route('admin.applications.index', array_merge(request()->except('page'), ['status' => 'submitted'])) }}" 
               class="p-4 rounded-xl border transition flex flex-col justify-between {{ $status === 'submitted' ? 'bg-amber-600 text-white border-amber-600 shadow-sm' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-300' }}">
                <span class="text-[11px] font-bold uppercase tracking-wider {{ $status === 'submitted' ? 'text-amber-100' : 'text-slate-500' }}">Submitted</span>
                <span class="text-2xl font-extrabold mt-1" x-text="stats.submitted">{{ number_format($stats['submitted']) }}</span>
            </a>

            <!-- Under Review -->
            <a href="{{ route('admin.applications.index', array_merge(request()->except('page'), ['status' => 'under_review'])) }}" 
               class="p-4 rounded-xl border transition flex flex-col justify-between {{ $status === 'under_review' ? 'bg-blue-600 text-white border-blue-600 shadow-sm' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-300' }}">
                <span class="text-[11px] font-bold uppercase tracking-wider {{ $status === 'under_review' ? 'text-blue-100' : 'text-slate-500' }}">Under Review</span>
                <span class="text-2xl font-extrabold mt-1" x-text="stats.under_review">{{ number_format($stats['under_review']) }}</span>
            </a>

            <!-- Incomplete -->
            <a href="{{ route('admin.applications.index', array_merge(request()->except('page'), ['status' => 'incomplete'])) }}" 
               class="p-4 rounded-xl border transition flex flex-col justify-between {{ $status === 'incomplete' ? 'bg-rose-600 text-white border-rose-600 shadow-sm' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-300' }}">
                <span class="text-[11px] font-bold uppercase tracking-wider {{ $status === 'incomplete' ? 'text-rose-100' : 'text-slate-500' }}">Incomplete</span>
                <span class="text-2xl font-extrabold mt-1" x-text="stats.incomplete">{{ number_format($stats['incomplete']) }}</span>
            </a>

            <!-- Approved -->
            <a href="{{ route('admin.applications.index', array_merge(request()->except('page'), ['status' => 'approved'])) }}" 
               class="p-4 rounded-xl border transition flex flex-col justify-between {{ $status === 'approved' ? 'bg-emerald-600 text-white border-emerald-600 shadow-sm' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-300' }}">
                <span class="text-[11px] font-bold uppercase tracking-wider {{ $status === 'approved' ? 'text-emerald-100' : 'text-slate-500' }}">Approved</span>
                <span class="text-2xl font-extrabold mt-1" x-text="stats.approved">{{ number_format($stats['approved']) }}</span>
            </a>

            <!-- Rejected -->
            <a href="{{ route('admin.applications.index', array_merge(request()->except('page'), ['status' => 'rejected'])) }}" 
               class="p-4 rounded-xl border transition flex flex-col justify-between {{ $status === 'rejected' ? 'bg-slate-700 text-white border-slate-700 shadow-sm' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-300' }}">
                <span class="text-[11px] font-bold uppercase tracking-wider {{ $status === 'rejected' ? 'text-slate-300' : 'text-slate-500' }}">Rejected</span>
                <span class="text-2xl font-extrabold mt-1" x-text="stats.rejected">{{ number_format($stats['rejected']) }}</span>
            </a>
        </div>

        <!-- Filters & Search Bar -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-4 mb-6">
            <form method="GET" action="{{ route('admin.applications.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                @if($status)
                    <input type="hidden" name="status" value="{{ $status }}">
                @endif

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
                           placeholder="Search student, ID, or scholarship..." 
                           {{ $search ? 'autofocus onfocus="this.setSelectionRange(this.value.length, this.value.length)"' : '' }}
                           oninput="clearTimeout(window._searchTimer); window._searchTimer = setTimeout(() => this.form.submit(), 400)"
                           class="w-full py-2 pl-9 pr-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                </div>

                <!-- Scholarship Select Filter -->
                <div class="sm:col-span-4">
                    <select name="scholarship_id" onchange="this.form.submit()" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 font-semibold focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                        <option value="">All Scholarship Programs</option>
                        @foreach($scholarships as $sch)
                            <option value="{{ $sch->id }}" {{ $scholarshipId == $sch->id ? 'selected' : '' }}>
                                {{ $sch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Academic Year Select Filter -->
                <div class="sm:col-span-3 flex items-center gap-2">
                    <select name="academic_year_id" onchange="this.form.submit()" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 font-semibold focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                        <option value="">All Academic Years</option>
                        @foreach($academicYears as $ay)
                            <option value="{{ $ay->id }}" {{ $academicYearId == $ay->id ? 'selected' : '' }}>
                                {{ $ay->name }}
                            </option>
                        @endforeach
                    </select>
                    @if($search || $scholarshipId || $academicYearId || $status)
                        <a href="{{ route('admin.applications.index') }}" class="px-2.5 py-1.5 text-xs font-bold text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition shrink-0" title="Reset Filters">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Applications Table Card -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3.5 w-10 text-center">
                                <input type="checkbox" @change="toggleAll($event)" class="rounded border-slate-300 text-[#6B0F1A] focus:ring-[#6B0F1A] cursor-pointer">
                            </th>
                            <th class="px-6 py-3.5">Student Information (Name / Student ID)</th>
                            <th class="px-6 py-3.5">Scholarship Program</th>
                            <th class="px-6 py-3.5">Submission Date</th>
                            <th class="px-6 py-3.5">Docs Status</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody id="applications-tbody" class="divide-y divide-slate-200">
                        @forelse($applications as $app)
                            <tr id="app-row-{{ $app->id }}" class="hover:bg-slate-50 transition">
                                <!-- Multi-Select Checkbox -->
                                <td class="px-4 py-4 text-center">
                                    <input type="checkbox" value="{{ $app->id }}" x-model="selectedIds" class="rounded border-slate-300 text-[#6B0F1A] focus:ring-[#6B0F1A] cursor-pointer">
                                </td>
                                <!-- Student Info -->
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900 text-sm">
                                        {{ $app->student->user->full_name }}
                                    </div>
                                    <div class="text-[11px] text-slate-500 font-mono">
                                        {{ $app->student->student_number }} • {{ $app->student->course }}
                                    </div>
                                </td>

                                <!-- Scholarship -->
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800">
                                        {{ $app->scholarship->name }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 font-medium">
                                        {{ $app->scholarship->academicYear->name ?? 'AY N/A' }}
                                    </div>
                                </td>

                                <!-- Submitted At -->
                                <td class="px-6 py-4 font-medium text-slate-600">
                                    {{ $app->submitted_at ? $app->submitted_at->format('M d, Y • h:i A') : 'N/A' }}
                                </td>

                                <!-- Docs count & verified badge -->
                                <td class="px-6 py-4">
                                    @php
                                        $verifiedDocs = $app->documents->where('status', 'verified')->count();
                                        $totalDocs = $app->documents->count();
                                        $needsResubmit = $app->documents->where('status', 'needs_resubmission')->count();
                                    @endphp
                                    <div class="text-xs font-bold text-slate-700">
                                        {{ $verifiedDocs }} / {{ $totalDocs }} Verified
                                    </div>
                                    @if($needsResubmit > 0)
                                        <span class="text-[10px] font-bold text-rose-600">
                                            {{ $needsResubmit }} document(s) flagged
                                        </span>
                                    @endif
                                </td>

                                <!-- Application Status Badge -->
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                                        @if($app->status === 'approved') bg-emerald-100 text-emerald-800 border border-emerald-300
                                        @elseif($app->status === 'rejected') bg-slate-200 text-slate-800 border border-slate-300
                                        @elseif($app->status === 'incomplete') bg-rose-100 text-rose-800 border border-rose-300
                                        @elseif($app->status === 'under_review') bg-blue-100 text-blue-800 border border-blue-300
                                        @else bg-amber-100 text-amber-800 border border-amber-300 @endif">
                                        {{ str_replace('_', ' ', $app->status) }}
                                    </span>
                                </td>

                                <!-- Action Buttons -->
                                <td class="px-6 py-4 text-right flex items-center justify-end gap-2">
                                    @if($app->status !== 'approved')
                                        <button type="button" 
                                                @click="approveApp({{ $app->id }}, '{{ addslashes($app->student->user->full_name) }}', '{{ csrf_token() }}')"
                                                class="inline-flex items-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-xs transition shadow-xs cursor-pointer"
                                                title="Quick Approve & Move to Active Scholars">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Approve
                                        </button>
                                    @endif
                                    <a href="{{ route('admin.applications.show', $app->id) }}" 
                                       class="inline-flex items-center px-3.5 py-1.5 bg-[#3B060F] text-white font-bold rounded-lg text-xs hover:bg-[#6B0F1A] transition shadow-xs">
                                        Review
                                        <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr id="empty-state-row">
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500 text-xs">
                                    <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    No student applications match your filter criteria.
                                </td>
                            </tr>
                        @endforelse
                        <tr id="empty-state-row" class="hidden">
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500 text-xs">
                                <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                All pending applications processed! No applications remaining in queue.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if($applications->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                    {{ $applications->links() }}
                </div>
            @endif
        </div>

        <!-- Floating Toast Alert -->
        <div x-show="toast.show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-4"
             class="fixed bottom-6 right-6 z-50 bg-[#3B060F] text-white p-4 rounded-2xl shadow-2xl border border-emerald-500/40 max-w-md flex items-start space-x-3"
             style="display: none;">
            <div class="h-9 w-9 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div class="flex-1">
                <h4 class="text-xs font-extrabold text-[#FFC107] uppercase tracking-wider">Application Approved</h4>
                <p class="text-xs font-semibold text-slate-200 mt-0.5" x-text="toast.message"></p>
                <template x-if="toast.scholarUrl">
                    <a :href="toast.scholarUrl" class="mt-2 inline-flex items-center text-xs font-extrabold text-emerald-300 hover:text-white underline">
                        View Active Scholar Profile &rarr;
                    </a>
                </template>
            </div>
            <button @click="toast.show = false" type="button" class="text-slate-400 hover:text-white transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Floating Bulk Action Bar -->
        <div x-show="selectedIds.length > 0"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-8"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-8"
             class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 bg-[#3B060F] text-white px-6 py-4 rounded-2xl shadow-2xl border border-emerald-500/50 flex items-center space-x-6"
             style="display: none;">
            <div class="flex items-center space-x-3 border-r border-emerald-700/60 pr-6">
                <div class="h-8 w-8 rounded-full bg-[#FFC107] text-[#3B060F] font-extrabold flex items-center justify-center text-xs" x-text="selectedIds.length">
                    0
                </div>
                <div>
                    <h4 class="text-xs font-extrabold text-white">Applications Selected</h4>
                    <p class="text-[10px] text-slate-300">Choose batch action for selected students</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.applications.bulk-status') }}" class="flex items-center space-x-3">
                @csrf
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="application_ids[]" :value="id">
                </template>

                <button type="submit" name="status" value="approved" onclick="return confirm('Are you sure you want to approve all selected applications and move them to Active Scholars?')" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold text-xs rounded-xl transition shadow-md flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                    Bulk Approve Selected
                </button>

                <button type="submit" name="status" value="rejected" onclick="return confirm('Are you sure you want to reject all selected applications?')" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-xl transition shadow-md flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Bulk Reject Selected
                </button>
            </form>

            <button @click="selectedIds = []" type="button" class="text-xs font-bold text-slate-300 hover:text-white underline cursor-pointer">
                Deselect All
            </button>
        </div>

    </div>

</x-app-layout>out>
