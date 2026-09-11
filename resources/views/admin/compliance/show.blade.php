<x-app-layout title="{{ $complianceRequest->title }} - CSU Lal-lo Admin">

    <div x-data="{ 
        extendModalOpen: false,
        reviewModalOpen: false,
        previewModalOpen: false,
        previewUrl: '',
        previewTitle: '',
        previewType: 'pdf',
        activeScholar: null,
        activeDocuments: [],
        openReviewModal(scholarData, documents) {
            this.activeScholar = scholarData;
            this.activeDocuments = documents;
            this.reviewModalOpen = true;
        },
        openDocPreview(url, title, filename) {
            this.previewUrl = url;
            this.previewTitle = title;
            const ext = (filename || '').split('.').pop().toLowerCase();
            this.previewType = ['jpg', 'jpeg', 'png', 'webp'].includes(ext) ? 'image' : 'pdf';
            this.previewModalOpen = true;
        }
    }">

        <!-- Breadcrumbs & Header Section -->
        <div class="mb-6">
            <div class="flex items-center gap-2 text-xs text-slate-500 font-semibold mb-2">
                <a href="{{ route('admin.compliance.index') }}" class="hover:text-slate-800 transition">Compliance Requests</a>
                <span>/</span>
                <span class="text-slate-700 font-bold truncate">{{ $complianceRequest->title }}</span>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $complianceRequest->title }}</h1>
                    <div class="flex flex-wrap items-center gap-2 mt-1.5 text-xs text-slate-500 font-medium">
                        <span class="font-bold text-[#3B060F] bg-emerald-50 px-2.5 py-0.5 rounded-md border border-emerald-200">
                            {{ $complianceRequest->scholarship->name }}
                        </span>
                        <span>•</span>
                        <span class="font-bold text-slate-700">{{ $complianceRequest->semester }} AY {{ $complianceRequest->school_year }}</span>
                        <span>•</span>
                        <span class="{{ $complianceRequest->scholarship->isContinuing() ? 'text-emerald-700' : 'text-blue-700' }} font-bold">
                            {{ $complianceRequest->scholarship->coverage_type_label }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <button @click="extendModalOpen = true" 
                            type="button" 
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white text-slate-700 font-bold text-xs rounded-xl border border-slate-200 hover:bg-slate-50 transition shadow-xs">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Extend Deadline
                    </button>

                    <a href="{{ route('admin.compliance.index') }}" 
                       class="inline-flex items-center gap-1 px-3.5 py-2 bg-slate-100 text-slate-700 font-bold text-xs rounded-xl hover:bg-slate-200 transition">
                        Back to List
                    </a>
                </div>
            </div>
        </div>

        <!-- Info & Instructions Banner -->
        @if($complianceRequest->instructions)
            <div class="mb-6 p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-700">
                <span class="font-extrabold uppercase tracking-wider text-slate-500 block mb-1">Instructions for Scholars:</span>
                <p class="leading-relaxed whitespace-pre-line">{{ $complianceRequest->instructions }}</p>
            </div>
        @endif

        <!-- Status Filter Tabs / Stat Counters -->
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3 mb-6">
            <!-- Total -->
            <a href="{{ route('admin.compliance.show', $complianceRequest->id) }}" 
               class="p-3 rounded-xl border transition flex flex-col justify-between {{ empty($statusFilter) ? 'bg-[#3B060F] text-white border-[#3B060F] shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-300' }}">
                <span class="text-[10px] font-extrabold uppercase tracking-wider {{ empty($statusFilter) ? 'text-[#FFC107]' : 'text-slate-500' }}">Assigned</span>
                <span class="text-xl font-extrabold mt-1">{{ number_format($stats['total_assigned']) }}</span>
            </a>

            <!-- Not Submitted -->
            <a href="{{ route('admin.compliance.show', [$complianceRequest->id, 'status' => 'not_submitted']) }}" 
               class="p-3 rounded-xl border transition flex flex-col justify-between {{ $statusFilter === 'not_submitted' ? 'bg-slate-700 text-white border-slate-700 shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-300' }}">
                <span class="text-[10px] font-extrabold uppercase tracking-wider {{ $statusFilter === 'not_submitted' ? 'text-slate-200' : 'text-slate-500' }}">Not Submitted</span>
                <span class="text-xl font-extrabold mt-1">{{ number_format($stats['not_submitted']) }}</span>
            </a>

            <!-- Partially Submitted -->
            <a href="{{ route('admin.compliance.show', [$complianceRequest->id, 'status' => 'partially_submitted']) }}" 
               class="p-3 rounded-xl border transition flex flex-col justify-between {{ $statusFilter === 'partially_submitted' ? 'bg-blue-600 text-white border-blue-600 shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-300' }}">
                <span class="text-[10px] font-extrabold uppercase tracking-wider {{ $statusFilter === 'partially_submitted' ? 'text-blue-100' : 'text-slate-500' }}">Partial</span>
                <span class="text-xl font-extrabold mt-1">{{ number_format($stats['partially_submitted']) }}</span>
            </a>

            <!-- Under Review -->
            <a href="{{ route('admin.compliance.show', [$complianceRequest->id, 'status' => 'under_review']) }}" 
               class="p-3 rounded-xl border transition flex flex-col justify-between {{ $statusFilter === 'under_review' ? 'bg-indigo-600 text-white border-indigo-600 shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-300' }}">
                <span class="text-[10px] font-extrabold uppercase tracking-wider {{ $statusFilter === 'under_review' ? 'text-indigo-100' : 'text-slate-500' }}">Under Review</span>
                <span class="text-xl font-extrabold mt-1">{{ number_format($stats['under_review']) }}</span>
            </a>

            <!-- Needs Correction -->
            <a href="{{ route('admin.compliance.show', [$complianceRequest->id, 'status' => 'needs_correction']) }}" 
               class="p-3 rounded-xl border transition flex flex-col justify-between {{ $statusFilter === 'needs_correction' ? 'bg-amber-600 text-white border-amber-600 shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-300' }}">
                <span class="text-[10px] font-extrabold uppercase tracking-wider {{ $statusFilter === 'needs_correction' ? 'text-amber-100' : 'text-slate-500' }}">Correction</span>
                <span class="text-xl font-extrabold mt-1">{{ number_format($stats['needs_correction']) }}</span>
            </a>

            <!-- Completed -->
            <a href="{{ route('admin.compliance.show', [$complianceRequest->id, 'status' => 'completed']) }}" 
               class="p-3 rounded-xl border transition flex flex-col justify-between {{ $statusFilter === 'completed' ? 'bg-emerald-600 text-white border-emerald-600 shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-300' }}">
                <span class="text-[10px] font-extrabold uppercase tracking-wider {{ $statusFilter === 'completed' ? 'text-emerald-100' : 'text-slate-500' }}">Completed</span>
                <span class="text-xl font-extrabold mt-1">{{ number_format($stats['completed']) }}</span>
            </a>

            <!-- Overdue -->
            <a href="{{ route('admin.compliance.show', [$complianceRequest->id, 'status' => 'overdue']) }}" 
               class="p-3 rounded-xl border transition flex flex-col justify-between {{ $statusFilter === 'overdue' ? 'bg-rose-600 text-white border-rose-600 shadow-xs' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-300' }}">
                <span class="text-[10px] font-extrabold uppercase tracking-wider {{ $statusFilter === 'overdue' ? 'text-rose-100' : 'text-slate-500' }}">Overdue</span>
                <span class="text-xl font-extrabold mt-1">{{ number_format($stats['overdue']) }}</span>
            </a>
        </div>

        <!-- Dynamic Search Form -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-4 mb-6">
            <form method="GET" action="{{ route('admin.compliance.show', $complianceRequest->id) }}" class="flex items-center gap-3">
                @if($statusFilter)
                    <input type="hidden" name="status" value="{{ $statusFilter }}">
                @endif

                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}" 
                           placeholder="Search scholar name, Student ID (e.g. 26-32424), or email..." 
                           {{ $search ? 'autofocus onfocus="this.setSelectionRange(this.value.length, this.value.length)"' : '' }}
                           oninput="clearTimeout(window._searchTimer); window._searchTimer = setTimeout(() => this.form.submit(), 400)"
                           class="w-full py-2 pl-9 pr-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                </div>

                @if($search || $statusFilter)
                    <a href="{{ route('admin.compliance.show', $complianceRequest->id) }}" class="px-3 py-2 text-xs font-bold text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition shrink-0">Reset</a>
                @endif
            </form>
        </div>

        <!-- Scholar Compliance Roster Table -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#3B060F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Scholar Compliance Matrix
                </h3>
                <span class="text-xs font-semibold text-slate-500">
                    Showing {{ $scholarCompliances->firstItem() ?? 0 }} to {{ $scholarCompliances->lastItem() ?? 0 }} of {{ $scholarCompliances->total() }} Scholars
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-100/70 text-[11px] font-bold text-slate-500 uppercase border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3">Scholar Grantee</th>
                            <th class="px-6 py-3">Student ID</th>
                            <th class="px-6 py-3">Course & Year</th>
                            <th class="px-6 py-3">Requirements Progress</th>
                            <th class="px-6 py-3">Overall Status</th>
                            <th class="px-6 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($scholarCompliances as $sc)
                            @php
                                $studentUser = $sc->scholar->student->user ?? null;
                                $docsKeyed = $sc->documents->keyBy('compliance_requirement_id');
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <!-- Scholar Grantee -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        @if($studentUser && $studentUser->profile_photo_url)
                                            <img src="{{ $studentUser->profile_photo_url }}" alt="{{ $studentUser->full_name }}" class="h-9 w-9 rounded-full object-cover border border-[#6B0F1A] shrink-0">
                                        @else
                                            <div class="h-9 w-9 rounded-full bg-[#3B060F] text-[#FFC107] font-bold flex items-center justify-center text-xs shrink-0 border border-[#FFC107]/30">
                                                {{ strtoupper(substr($studentUser->first_name ?? 'S', 0, 1) . substr($studentUser->last_name ?? 'C', 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-extrabold text-slate-900 text-sm">
                                                {{ $studentUser->full_name ?? 'N/A' }}
                                            </div>
                                            <div class="text-[11px] text-slate-400 font-medium">
                                                {{ $studentUser->email ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Student ID -->
                                <td class="px-6 py-4">
                                    <span class="font-mono font-bold text-slate-800 text-xs bg-slate-100 px-2.5 py-1 rounded-md border border-slate-200">
                                        {{ $sc->scholar->student->student_number ?? 'N/A' }}
                                    </span>
                                </td>

                                <!-- Course & Year -->
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800 text-xs">
                                        {{ $sc->scholar->student->course ?? 'N/A' }}
                                    </div>
                                    <div class="text-[11px] text-slate-500 font-medium mt-0.5">
                                        {{ $sc->scholar->student->year_level ?? 'N/A' }}
                                    </div>
                                </td>

                                <!-- Requirements Progress Badges Matrix -->
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1.5 max-w-sm">
                                        @foreach($complianceRequest->requirements as $reqItem)
                                            @php
                                                $doc = $docsKeyed->get($reqItem->id);
                                            @endphp
                                            @if(!$doc)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-500 border border-slate-200" title="{{ $reqItem->name }}: Not Uploaded">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span>
                                                    {{ Str::limit($reqItem->name, 14) }}
                                                </span>
                                            @elseif($doc->verification_status === 'verified')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300" title="{{ $reqItem->name }}: Verified">
                                                    <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                    {{ Str::limit($reqItem->name, 14) }}
                                                </span>
                                            @elseif($doc->verification_status === 'needs_correction')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300" title="{{ $reqItem->name }}: Needs Correction">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                                    {{ Str::limit($reqItem->name, 14) }}
                                                </span>
                                            @elseif($doc->verification_status === 'rejected')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-300" title="{{ $reqItem->name }}: Rejected">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                                    {{ Str::limit($reqItem->name, 14) }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-800 border border-indigo-300" title="{{ $reqItem->name }}: Uploaded / Pending Review">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-indigo-500"></span>
                                                    {{ Str::limit($reqItem->name, 14) }}
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                </td>

                                <!-- Overall Status Badge -->
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                                        @if($sc->status === 'completed') bg-emerald-100 text-emerald-800 border border-emerald-300
                                        @elseif($sc->status === 'under_review' || $sc->status === 'submitted') bg-indigo-100 text-indigo-800 border border-indigo-300
                                        @elseif($sc->status === 'needs_correction') bg-amber-100 text-amber-800 border border-amber-300
                                        @elseif($sc->status === 'partially_submitted') bg-blue-100 text-blue-800 border border-blue-300
                                        @elseif($sc->status === 'overdue') bg-rose-100 text-rose-800 border border-rose-300
                                        @else bg-slate-100 text-slate-700 border border-slate-300 @endif">
                                        {{ str_replace('_', ' ', $sc->status) }}
                                    </span>
                                </td>

                                <!-- Action: Review Modal -->
                                <td class="px-6 py-4 text-right">
                                    <button type="button"
                                            @click="openReviewModal(
                                                {{ json_encode([
                                                    'name' => $studentUser->full_name ?? 'N/A',
                                                    'student_number' => $sc->scholar->student->student_number ?? 'N/A',
                                                    'course' => $sc->scholar->student->course ?? 'N/A',
                                                    'status' => $sc->status
                                                ]) }},
                                                {{ json_encode($complianceRequest->requirements->map(function($req) use ($docsKeyed) {
                                                    $doc = $docsKeyed->get($req->id);
                                                    return [
                                                        'requirement_id' => $req->id,
                                                        'requirement_name' => $req->name,
                                                        'requirement_instruction' => $req->instruction,
                                                        'document_id' => $doc ? $doc->id : null,
                                                        'original_filename' => $doc ? $doc->original_filename : null,
                                                        'view_url' => $doc ? route('admin.compliance.documents.view', $doc->id) : null,
                                                        'verification_status' => $doc ? $doc->verification_status : 'not_submitted',
                                                        'student_remarks' => $doc ? $doc->student_remarks : null,
                                                        'admin_remarks' => $doc ? $doc->admin_remarks : null,
                                                        'updated_at' => $doc ? $doc->updated_at->format('M d, Y h:i A') : null,
                                                    ];
                                                })) }}
                                            )"
                                            class="inline-flex items-center px-3 py-1.5 bg-[#3B060F] text-white font-bold rounded-lg text-xs hover:bg-[#6B0F1A] transition shadow-xs cursor-pointer">
                                        Inspect / Verify
                                        <svg class="w-3.5 h-3.5 ml-1 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500 text-xs">
                                    No scholars found matching the selected filter criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($scholarCompliances->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                    {{ $scholarCompliances->links() }}
                </div>
            @endif
        </div>

        <!-- ========================================== -->
        <!-- EXTEND DEADLINE MODAL -->
        <!-- ========================================== -->
        <div x-show="extendModalOpen" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             style="display: none;" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">

            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="extendModalOpen = false"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 text-left overflow-hidden border border-slate-200"
                     @click.away="extendModalOpen = false">

                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="text-base font-extrabold text-slate-900">Extend Compliance Deadline</h3>
                        <button @click="extendModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('admin.compliance.extend', $complianceRequest->id) }}" class="mt-4 space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1">
                                Current Deadline: <span class="text-slate-900 font-bold">{{ $complianceRequest->deadline->format('F d, Y') }}</span>
                            </label>
                            <label class="block text-xs font-extrabold text-slate-700 uppercase tracking-wider mb-1 mt-3">
                                New Submission Deadline <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   name="deadline" 
                                   min="{{ date('Y-m-d') }}"
                                   value="{{ date('Y-m-d', strtotime('+7 days')) }}" 
                                   required
                                   class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                            <button type="button" @click="extendModalOpen = false" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold text-xs rounded-xl hover:bg-slate-200 transition">
                                Cancel
                            </button>
                            <button type="submit" class="px-5 py-2 bg-amber-600 text-white font-extrabold text-xs rounded-xl hover:bg-amber-700 transition shadow-md">
                                Update Deadline
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- REVIEW & VERIFY DOCUMENTS MODAL -->
        <!-- ========================================== -->
        <div x-show="reviewModalOpen" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             style="display: none;" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">

            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs" @click="reviewModalOpen = false"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative bg-white rounded-2xl shadow-2xl max-w-3xl w-full p-6 text-left overflow-hidden border border-slate-200 max-h-[90vh] flex flex-col"
                     @click.away="reviewModalOpen = false">

                    <!-- Modal Header -->
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 shrink-0">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-extrabold text-slate-900" x-text="activeScholar?.name"></h3>
                                <span class="font-mono text-xs font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded border border-slate-200" x-text="activeScholar?.student_number"></span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">
                                <span x-text="activeScholar?.course"></span> • 
                                <span class="capitalize font-bold text-slate-700" x-text="activeScholar?.status?.replace('_', ' ')"></span>
                            </p>
                        </div>
                        <button @click="reviewModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body: Document Review Checklist -->
                    <div class="mt-4 space-y-4 overflow-y-auto flex-1 pr-1">
                        <template x-for="(doc, idx) in activeDocuments" :key="idx">
                            <div class="p-4 rounded-2xl border transition"
                                 :class="{
                                     'bg-emerald-50/50 border-emerald-200': doc.verification_status === 'verified',
                                     'bg-amber-50/50 border-amber-200': doc.verification_status === 'needs_correction',
                                     'bg-rose-50/50 border-rose-200': doc.verification_status === 'rejected',
                                     'bg-slate-50 border-slate-200': doc.verification_status === 'pending' || doc.verification_status === 'not_submitted'
                                 }">
                                
                                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-extrabold text-slate-900 text-xs uppercase tracking-wider" x-text="doc.requirement_name"></h4>
                                            
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider"
                                                  :class="{
                                                      'bg-emerald-100 text-emerald-800 border border-emerald-300': doc.verification_status === 'verified',
                                                      'bg-amber-100 text-amber-800 border border-amber-300': doc.verification_status === 'needs_correction',
                                                      'bg-rose-100 text-rose-800 border border-rose-300': doc.verification_status === 'rejected',
                                                      'bg-indigo-100 text-indigo-800 border border-indigo-300': doc.verification_status === 'pending',
                                                      'bg-slate-200 text-slate-600': doc.verification_status === 'not_submitted'
                                                  }"
                                                  x-text="doc.verification_status.replace('_', ' ')"></span>
                                        </div>

                                        <p class="text-[11px] text-slate-500 mt-1" x-show="doc.requirement_instruction" x-text="doc.requirement_instruction"></p>

                                        <!-- Uploaded File Info -->
                                        <div class="mt-2" x-show="doc.document_id">
                                            <div class="flex items-center gap-2 text-xs font-semibold text-slate-700">
                                                <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                </svg>
                                                <span class="font-mono truncate" x-text="doc.original_filename"></span>
                                                <span class="text-[10px] text-slate-400 font-normal" x-text="'• Submitted: ' + doc.updated_at"></span>
                                            </div>

                                            <p class="text-[11px] text-slate-600 italic mt-1 bg-white p-2 rounded-lg border border-slate-200" x-show="doc.student_remarks">
                                                <strong>Student remarks:</strong> <span x-text="doc.student_remarks"></span>
                                            </p>
                                        </div>

                                        <div class="mt-2 text-xs text-slate-400 italic" x-show="!doc.document_id">
                                            No file uploaded yet by student.
                                        </div>
                                    </div>

                                    <!-- Document Preview in Browser -->
                                    <div class="shrink-0 flex items-center gap-2" x-show="doc.document_id">
                                        <button type="button" 
                                                @click="openDocPreview(doc.view_url, doc.requirement_name, doc.original_filename)"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-300 text-slate-700 text-xs font-bold rounded-lg hover:bg-slate-100 transition shadow-xs cursor-pointer">
                                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View Document
                                        </button>
                                    </div>
                                </div>

                                <!-- Inline Verification Form for this Document -->
                                <div class="mt-3 pt-3 border-t border-slate-200/60" x-show="doc.document_id" x-data="{ reviewFormOpen: false, remarks: doc.admin_remarks || '' }">
                                    <form method="POST" :action="'/admin/compliance-documents/' + doc.document_id + '/verify'" class="space-y-2">
                                        @csrf

                                        <div x-show="doc.admin_remarks" class="text-[11px] text-amber-800 bg-amber-50 p-2 rounded-lg border border-amber-200 mb-2">
                                            <strong>Previous Admin Remark:</strong> <span x-text="doc.admin_remarks"></span>
                                        </div>

                                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                            <input type="text" 
                                                   name="admin_remarks" 
                                                   x-model="remarks" 
                                                   placeholder="Remark or deficiency reason (required for Correction/Reject)..." 
                                                   class="flex-1 py-1 px-2.5 bg-white border border-slate-200 rounded-lg text-xs text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">

                                            <div class="flex items-center gap-1.5 shrink-0">
                                                <!-- Verify Button -->
                                                <button type="submit" 
                                                        name="verification_status" 
                                                        value="verified"
                                                        class="px-2.5 py-1 bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold text-xs rounded-lg transition shadow-xs flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Verify
                                                </button>

                                                <!-- Needs Correction Button -->
                                                <button type="submit" 
                                                        name="verification_status" 
                                                        value="needs_correction"
                                                        class="px-2.5 py-1 bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs rounded-lg transition shadow-xs flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                    </svg>
                                                    Correction
                                                </button>

                                                <!-- Reject Button -->
                                                <button type="submit" 
                                                        name="verification_status" 
                                                        value="rejected"
                                                        class="px-2.5 py-1 bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs rounded-lg transition shadow-xs">
                                                    Reject
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </template>
                    </div>

                    <!-- Modal Footer -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end shrink-0">
                        <button type="button" @click="reviewModalOpen = false; window.location.reload()" class="px-5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                            Close & Refresh List
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- VIEW-ONLY DOCUMENT MODAL PREVIEWER -->
        <!-- ========================================== -->
        <div x-show="previewModalOpen"
             class="fixed inset-0 z-50 overflow-y-auto"
             style="display: none;"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">

            <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-xs" @click="previewModalOpen = false"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative bg-white rounded-2xl shadow-2xl max-w-4xl w-full p-5 text-left overflow-hidden border border-slate-200 flex flex-col max-h-[92vh]"
                     @click.away="previewModalOpen = false">
                     
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 shrink-0">
                        <div class="flex items-center space-x-2.5">
                            <div class="p-2 bg-emerald-50 text-emerald-800 rounded-lg">
                                <svg class="w-4 h-4 text-[#3B060F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-900" x-text="previewTitle"></h3>
                                <p class="text-[11px] text-slate-400">Compliance Document Preview (View only)</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <a :href="previewUrl" target="_blank" class="px-2.5 py-1 text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition" title="Open in new window">
                                Open in new window ↗
                            </a>
                            <button @click="previewModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Modal Body (Preview frame) -->
                    <div class="mt-3 flex-1 overflow-hidden rounded-xl bg-slate-100 flex items-center justify-center min-h-[450px]">
                        <template x-if="previewType === 'pdf'">
                            <iframe :src="previewUrl + '#toolbar=0&navpanes=0'" class="w-full h-[70vh] rounded-xl border border-slate-200 bg-white" frameborder="0"></iframe>
                        </template>
                        <template x-if="previewType === 'image'">
                            <div class="p-4 flex items-center justify-center w-full h-[70vh] overflow-auto">
                                <img :src="previewUrl" :alt="previewTitle" class="max-h-full max-w-full object-contain rounded-lg shadow-sm border border-slate-200">
                            </div>
                        </template>
                    </div>

                </div>
            </div>
        </div>

    </div>

</x-app-layout>
