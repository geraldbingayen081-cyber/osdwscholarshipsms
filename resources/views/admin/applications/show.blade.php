<x-app-layout title="Review Application - CSU Lal-lo Admin">

    <div x-data="{ 
        docModalOpen: false, 
        activeDocUrl: '', 
        activeDocTitle: '', 
        activeDocType: 'pdf',
        currentStatus: '{{ $application->status }}',
        currentStatusLabel: '{{ ucfirst(str_replace('_', ' ', $application->status)) }}',
        scholarUrl: '{{ $application->scholar ? route('admin.scholars.show', $application->scholar->id) : '' }}',
        toast: { show: false, message: '' },
        async submitStatusForm(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    this.currentStatus = data.status;
                    this.currentStatusLabel = data.status_label;
                    if (data.scholar_url) {
                        this.scholarUrl = data.scholar_url;
                    }
                    this.toast.message = data.message;
                    this.toast.show = true;
                    setTimeout(() => { this.toast.show = false; }, 7000);
                } else {
                    alert(data.message || 'Failed to update status.');
                }
            } catch (err) {
                console.error(err);
                form.submit();
            }
        }
    }">

        <!-- Breadcrumb Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2 text-xs font-semibold text-slate-500 mb-1">
                    <a href="{{ route('admin.applications.index') }}" class="hover:text-[#6B0F1A] transition">&larr; Applications List</a>
                    <span>/</span>
                    <span class="text-slate-800">Application #{{ $application->id }}</span>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                    Review Application: {{ $application->student->user->full_name }}
                </h1>
            </div>

            <!-- Status Header Badge -->
            <div class="flex items-center space-x-3">
                <span class="text-xs font-bold text-slate-500">Current Status:</span>
                <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-extrabold uppercase tracking-wider transition-all"
                    :class="{
                        'bg-emerald-100 text-emerald-800 border border-emerald-300': currentStatus === 'approved',
                        'bg-slate-200 text-slate-800 border border-slate-300': currentStatus === 'rejected',
                        'bg-rose-100 text-rose-800 border border-rose-300': currentStatus === 'incomplete',
                        'bg-blue-100 text-blue-800 border border-blue-300': currentStatus === 'under_review',
                        'bg-amber-100 text-amber-800 border border-amber-300': currentStatus === 'submitted'
                    }"
                    x-text="currentStatusLabel">
                    {{ str_replace('_', ' ', $application->status) }}
                </span>
            </div>
        </div>

        <!-- Main 2-Column Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- LEFT 2 COLUMNS: Student Info, Scholarship Info, Submitted Documents -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Student Profile Summary Card -->
                <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-6">
                    <div class="flex items-center space-x-4 border-b border-slate-100 pb-4 mb-4">
                        @if($application->student->user->profile_photo_url)
                            <img src="{{ $application->student->user->profile_photo_url }}" alt="{{ $application->student->user->full_name }}" class="h-14 w-14 rounded-full object-cover border-2 border-[#6B0F1A] shadow-xs shrink-0">
                        @else
                            <div class="h-12 w-12 rounded-full bg-[#3B060F] text-[#FFC107] font-extrabold flex items-center justify-center text-base border border-[#FFC107]/40 shadow-xs shrink-0">
                                {{ strtoupper(substr($application->student->user->first_name, 0, 1) . substr($application->student->user->last_name, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <h2 class="text-lg font-extrabold text-slate-900 leading-tight">
                                {{ $application->student->user->full_name }}
                            </h2>
                            <p class="text-xs text-slate-500 font-semibold mt-0.5">
                                Student ID: <span class="font-mono text-slate-800">{{ $application->student->student_number }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Course & Major</span>
                            <span class="font-bold text-slate-800 text-sm mt-0.5 block">{{ $application->student->course }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Year Level</span>
                            <span class="font-bold text-slate-800 text-sm mt-0.5 block">{{ $application->student->year_level }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Email Address</span>
                            <span class="font-bold text-slate-800 text-sm mt-0.5 block">{{ $application->student->user->email }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Contact Number</span>
                            <span class="font-bold text-slate-800 text-sm mt-0.5 block">{{ $application->student->contact_number ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Scholarship Program Applied For -->
                <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-6">
                    <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4">
                        Scholarship Program Details
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Program Name</span>
                            <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ $application->scholarship->name }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Provider / Grantor</span>
                            <span class="font-semibold text-slate-800 text-sm mt-0.5 block">{{ $application->scholarship->provider }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Coverage Type</span>
                            <span class="inline-flex items-center gap-1.5 font-bold text-xs mt-0.5 px-2.5 py-1 rounded-lg {{ $application->scholarship->isContinuing() ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-blue-50 text-blue-800 border border-blue-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $application->scholarship->isContinuing() ? 'bg-emerald-500' : 'bg-blue-500' }}"></span>
                                {{ $application->scholarship->coverage_type_label }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">School Year</span>
                            <span class="font-semibold text-slate-800 text-sm mt-0.5 block">{{ $application->scholarship->school_year_label }}</span>
                        </div>
                        <div class="sm:col-span-2">
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Benefits</span>
                            <span class="font-semibold text-slate-800 text-sm mt-0.5 block">{{ $application->scholarship->benefits }}</span>
                        </div>
                    </div>
                </div>

                <!-- Submitted Documents Review Card -->
                <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                        <h3 class="text-sm font-bold text-slate-900">
                            Submitted Requirements & Documents
                        </h3>
                        <span class="text-xs font-semibold text-slate-500">
                            {{ $application->documents->where('status', 'verified')->count() }} / {{ $application->documents->count() }} Verified
                        </span>
                    </div>

                    <div class="space-y-4">
                        @forelse($application->documents as $doc)
                            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 flex flex-col space-y-3">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-900">
                                            {{ $doc->requirement->requirement_name ?? 'Document Requirement' }}
                                        </h4>
                                        <p class="text-[11px] text-slate-500 font-mono mt-0.5">
                                            File: {{ $doc->original_filename }}
                                        </p>
                                    </div>

                                    <!-- Status pill -->
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider
                                        @if($doc->status === 'verified') bg-emerald-100 text-emerald-800 border border-emerald-200
                                        @elseif($doc->status === 'needs_resubmission') bg-rose-100 text-rose-800 border border-rose-200
                                        @else bg-amber-100 text-amber-800 border border-amber-200 @endif">
                                        {{ str_replace('_', ' ', $doc->status) }}
                                    </span>
                                </div>

                                <!-- Document View & Verification Actions -->
                                <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-slate-200/60">
                                    <button type="button" 
                                            @click="docModalOpen = true; activeDocUrl = '{{ route('admin.applications.documents.download', $doc->id) }}'; activeDocTitle = '{{ addslashes($doc->requirement->requirement_name ?? $doc->original_filename) }}'; activeDocType = '{{ \Illuminate\Support\Str::endsWith(strtolower($doc->original_filename), ['.jpg', '.jpeg', '.png']) ? 'image' : 'pdf' }}'" 
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#3B060F] text-white font-extrabold text-xs rounded-xl hover:bg-[#6B0F1A] transition shadow-xs cursor-pointer">
                                        <svg class="w-4 h-4 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        View Document
                                    </button>

                                    <!-- Form to update document status -->
                                    <form method="POST" action="{{ route('admin.applications.documents.verify', $doc->id) }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
                                        @csrf
                                        <input type="text" 
                                               name="remarks" 
                                               value="{{ $doc->remarks }}"
                                               placeholder="Remarks / Reason for resubmission..." 
                                               class="text-[11px] py-1.5 px-2.5 bg-white border border-slate-300 rounded-lg text-slate-700 w-full sm:w-48 focus:ring-1 focus:ring-[#6B0F1A] focus:outline-none">
                                        
                                        <div class="flex items-center gap-2 shrink-0">
                                            <button type="submit" name="status" value="verified" 
                                                    class="flex-1 sm:flex-none px-3 py-1.5 bg-emerald-600 text-white font-bold text-[11px] rounded-lg hover:bg-emerald-700 transition text-center cursor-pointer">
                                                Verify
                                            </button>

                                            <button type="submit" name="status" value="needs_resubmission" 
                                                    class="flex-1 sm:flex-none px-3 py-1.5 bg-rose-600 text-white font-bold text-[11px] rounded-lg hover:bg-rose-700 transition text-center cursor-pointer">
                                                Reject Doc
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-slate-500 text-xs">
                                No documents submitted for this application.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: Application Status Decision Card -->
            <div class="space-y-6">

                <!-- Decision Box -->
                <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-6 sticky top-6">
                    <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4">
                        Application Review & Action
                    </h3>

                    <form method="POST" @submit="submitStatusForm" action="{{ route('admin.applications.update-status', $application->id) }}" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Update Status</label>
                            <select name="status" x-model="currentStatus" class="w-full py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                                <option value="submitted">Submitted</option>
                                <option value="under_review">Under Review</option>
                                <option value="incomplete">Incomplete Document(s)</option>
                                <option value="approved">Approved (Enroll as Scholar)</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Admin Remarks / Feedback</label>
                            <textarea name="remarks" 
                                      rows="4" 
                                      placeholder="Enter reviewer remarks or instructions for the student..." 
                                      class="w-full p-3 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">{{ old('remarks', $application->remarks) }}</textarea>
                        </div>

                        <button type="submit" class="w-full py-2.5 bg-[#3B060F] text-white font-extrabold text-xs rounded-xl hover:bg-[#6B0F1A] transition shadow-md cursor-pointer">
                            Save Decision & Notify Student
                        </button>
                    </form>

                    <div x-show="currentStatus === 'approved'" class="mt-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-xs" style="{{ $application->status === 'approved' ? '' : 'display: none;' }}">
                        <div class="font-bold text-emerald-800 flex items-center">
                            <svg class="w-4 h-4 mr-1.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Active Scholar Enrolled
                        </div>
                        <p class="text-slate-600 mt-1 text-[11px]">
                            This student has been approved and is listed in the Active Scholars roster.
                        </p>
                        <template x-if="scholarUrl">
                            <a :href="scholarUrl" class="mt-2 inline-block font-bold text-[#3B060F] hover:underline text-[11px]">
                                View Scholar Profile &rarr;
                            </a>
                        </template>
                    </div>
                </div>

            </div>

        </div>

        <!-- In-System Read-Only Document Viewer Modal -->
        <div x-show="docModalOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @keydown.escape.window="docModalOpen = false"
             class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-slate-900/80 backdrop-blur-sm"
             style="display: none;">
            
            <div @click.away="docModalOpen = false" 
                 class="bg-white w-full max-w-5xl h-[88vh] rounded-2xl shadow-2xl overflow-hidden flex flex-col border border-slate-200">
                
                <!-- Modal Header with Title & Explicit "X" Close Button -->
                <div class="px-6 py-4 bg-[#3B060F] text-white flex items-center justify-between border-b border-[#480913] shrink-0">
                    <div class="flex items-center space-x-3 overflow-hidden">
                        <div class="h-9 w-9 rounded-xl bg-white/10 flex items-center justify-center text-[#FFC107] shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="overflow-hidden">
                            <h3 class="text-sm font-extrabold text-white truncate" x-text="activeDocTitle">Document Viewer</h3>
                            <p class="text-[10px] text-[#FFC107] font-semibold">CSU Lal-lo Scholarship Portal • System In-App Viewer</p>
                        </div>
                    </div>

                    <!-- Exit / Close Button ("X" Button) -->
                    <button @click="docModalOpen = false" 
                            type="button" 
                            title="Close Viewer (Esc)"
                            class="px-3.5 py-1.5 bg-red-600/90 hover:bg-red-600 text-white text-xs font-extrabold rounded-xl transition flex items-center gap-1.5 shadow-sm border border-red-500/40 cursor-pointer">
                        <span>Close</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Modal Body Container (View-Only viewport) -->
                <div class="flex-1 bg-slate-900 relative overflow-hidden flex items-center justify-center">
                    <template x-if="activeDocType === 'pdf'">
                        <iframe :src="activeDocUrl + '#toolbar=0&navpanes=0'" class="w-full h-full border-0 bg-white" title="PDF Document Viewer"></iframe>
                    </template>

                    <template x-if="activeDocType === 'image'">
                        <div class="w-full h-full p-4 flex items-center justify-center overflow-auto">
                            <img :src="activeDocUrl" alt="Document Image" class="max-h-full max-w-full object-contain rounded-lg shadow-xl border border-slate-700 select-none pointer-events-none" oncontextmenu="return false;">
                        </div>
                    </template>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-3 bg-slate-100 border-t border-slate-200 flex items-center justify-between text-xs text-slate-600 shrink-0">
                    <span class="font-medium">Document View-Only Mode Active</span>
                    <button @click="docModalOpen = false" type="button" class="px-4 py-1.5 bg-slate-800 text-white font-extrabold rounded-xl hover:bg-slate-900 transition cursor-pointer">
                        Close Viewer (Esc)
                    </button>
                </div>
            </div>
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
                <h4 class="text-xs font-extrabold text-[#FFC107] uppercase tracking-wider">Status Updated</h4>
                <p class="text-xs font-semibold text-slate-200 mt-0.5" x-text="toast.message"></p>
                <template x-if="currentStatus === 'approved' && scholarUrl">
                    <a :href="scholarUrl" class="mt-2 inline-flex items-center text-xs font-extrabold text-emerald-300 hover:text-white underline">
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

    </div>

</x-app-layout>
