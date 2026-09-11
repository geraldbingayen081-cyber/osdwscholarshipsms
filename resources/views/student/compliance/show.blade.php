<x-app-layout title="{{ $complianceRequest->title }} - CSU Lal-lo">

    @php
        $docsKeyed = $scholarCompliance->documents->keyBy('compliance_requirement_id');
        $totalReqs = $complianceRequest->requirements->count();
        $verifiedCount = $scholarCompliance->documents->where('verification_status', 'verified')->count();
        $submittedCount = $scholarCompliance->documents->count();
    @endphp

    <div x-data="{ 
        previewModalOpen: false,
        previewUrl: '',
        previewTitle: '',
        previewType: 'pdf',
        openPreview(url, title, filename) {
            this.previewUrl = url;
            this.previewTitle = title;
            const ext = (filename || '').split('.').pop().toLowerCase();
            this.previewType = ['jpg', 'jpeg', 'png', 'webp'].includes(ext) ? 'image' : 'pdf';
            this.previewModalOpen = true;
        }
    }">

        <!-- Breadcrumb & Header -->
        <div class="mb-6">
            <div class="flex items-center gap-2 text-xs text-slate-500 font-semibold mb-2">
                <a href="{{ route('student.compliance.index') }}" class="hover:text-slate-800 transition">Compliance Requests</a>
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
                    <a href="{{ route('student.compliance.index') }}" class="inline-flex items-center gap-1 px-3.5 py-2 bg-slate-100 text-slate-700 font-bold text-xs rounded-xl hover:bg-slate-200 transition">
                        &larr; Back to List
                    </a>
                </div>
            </div>
        </div>

        <!-- Overall Status & Deadline Banner -->
        <div class="mb-6 rounded-2xl p-5 border shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4
            @if($scholarCompliance->status === 'completed') bg-emerald-50 border-emerald-200
            @elseif($scholarCompliance->status === 'needs_correction') bg-amber-50 border-amber-300
            @elseif($scholarCompliance->status === 'overdue') bg-rose-50 border-rose-300
            @else bg-slate-50 border-slate-200 @endif">
            
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Your Compliance Status:</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-extrabold uppercase tracking-wider
                        @if($scholarCompliance->status === 'completed') bg-emerald-600 text-white
                        @elseif($scholarCompliance->status === 'needs_correction') bg-amber-500 text-white animate-pulse
                        @elseif($scholarCompliance->status === 'overdue') bg-rose-600 text-white
                        @elseif($scholarCompliance->status === 'under_review' || $scholarCompliance->status === 'submitted') bg-indigo-600 text-white
                        @elseif($scholarCompliance->status === 'partially_submitted') bg-blue-600 text-white
                        @else bg-slate-700 text-white @endif">
                        {{ str_replace('_', ' ', $scholarCompliance->status) }}
                    </span>
                </div>

                <p class="text-xs text-slate-600 mt-1 font-medium">
                    @if($scholarCompliance->status === 'completed')
                        🎉 Excellent! All requested compliance requirements have been verified by OSDW.
                    @elseif($scholarCompliance->status === 'needs_correction')
                        ⚠️ One or more documents require correction or re-upload. Please review the admin remarks below.
                    @elseif($scholarCompliance->status === 'overdue')
                        ⚠️ The submission deadline has passed. Please upload any missing documents immediately.
                    @elseif($scholarCompliance->status === 'submitted' || $scholarCompliance->status === 'under_review')
                        ⏳ All files uploaded! OSDW is currently evaluating and verifying your submissions.
                    @else
                        Please upload the required files below to maintain your periodic scholarship compliance.
                    @endif
                </p>
            </div>

            <div class="text-left md:text-right shrink-0">
                <div class="text-xs font-bold text-slate-500 uppercase tracking-wider">Submission Deadline</div>
                <div class="text-sm font-extrabold {{ $complianceRequest->isPastDeadline() ? 'text-red-600' : 'text-slate-900' }} mt-0.5">
                    {{ $complianceRequest->deadline->format('F d, Y') }}
                </div>
                <div class="text-[11px] mt-0.5">
                    @if($complianceRequest->isPastDeadline())
                        <span class="text-red-600 font-bold">Past Deadline</span>
                    @else
                        <span class="text-emerald-700 font-bold">{{ $complianceRequest->days_remaining }} remaining</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- OSDW Instructions Notice (if present) -->
        @if($complianceRequest->instructions)
            <div class="mb-6 p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs text-xs text-slate-700">
                <h4 class="font-extrabold text-[#3B060F] uppercase tracking-wider mb-1 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    OSDW Guidelines & Reminders
                </h4>
                <p class="leading-relaxed whitespace-pre-line text-slate-600">{{ $complianceRequest->instructions }}</p>
            </div>
        @endif

        <!-- Requirements Checklist Cards -->
        <div class="space-y-6">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <svg class="w-4 h-4 text-[#3B060F]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                Required Compliance Documents ({{ $verifiedCount }}/{{ $totalReqs }} Verified)
            </h3>

            @foreach($complianceRequest->requirements as $index => $req)
                @php
                    $doc = $docsKeyed->get($req->id);
                    $isVerified = $doc && $doc->verification_status === 'verified';
                    $needsCorrection = $doc && $doc->verification_status === 'needs_correction';
                    $isPending = $doc && $doc->verification_status === 'pending';
                    $isRejected = $doc && $doc->verification_status === 'rejected';
                @endphp

                <div class="bg-white rounded-2xl shadow-xs border p-5 transition
                    @if($isVerified) border-emerald-200 bg-emerald-50/20
                    @elseif($needsCorrection) border-amber-300 bg-amber-50/30
                    @elseif($isRejected) border-rose-300 bg-rose-50/30
                    @else border-slate-200 @endif">

                    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 pb-4 border-b border-slate-100">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="h-6 w-6 rounded-full bg-slate-100 text-slate-700 font-extrabold text-xs flex items-center justify-center border border-slate-200">
                                    {{ $index + 1 }}
                                </span>
                                <h4 class="text-sm font-extrabold text-slate-900">
                                    {{ $req->name }}
                                </h4>
                                @if($req->is_required)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-red-100 text-red-800 border border-red-200">
                                        Required
                                    </span>
                                @endif
                            </div>

                            @if($req->instruction)
                                <p class="text-xs text-slate-500 mt-1 pl-8">
                                    {{ $req->instruction }}
                                </p>
                            @endif
                        </div>

                        <!-- Status Badge -->
                        <div class="pl-8 md:pl-0 shrink-0">
                            @if($isVerified)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Verified & Accepted
                                </span>
                            @elseif($needsCorrection)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300 animate-pulse">
                                    ⚠️ Needs Correction
                                </span>
                            @elseif($isRejected)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                    ✕ Rejected
                                </span>
                            @elseif($isPending)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                                    ⏳ Pending OSDW Review
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                    Not Uploaded
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Admin Remarks Alert (if Needs Correction or Rejected) -->
                    @if(($needsCorrection || $isRejected) && $doc->admin_remarks)
                        <div class="mt-4 p-3.5 rounded-xl bg-amber-50 border border-amber-300 text-amber-900 text-xs">
                            <div class="font-extrabold flex items-center gap-1.5 text-amber-800">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                OSDW Evaluation Remark / Correction Required:
                            </div>
                            <p class="mt-1 font-medium pl-5.5 leading-relaxed">{{ $doc->admin_remarks }}</p>
                        </div>
                    @endif

                    <!-- Existing Uploaded File Info & In-Browser Preview Button -->
                    @if($doc)
                        <div class="mt-4 p-3 rounded-xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center space-x-3 text-xs">
                                <div class="p-2 rounded-lg bg-white border border-slate-200 text-slate-600 shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div class="overflow-hidden">
                                    <p class="font-bold text-slate-800 font-mono truncate">{{ $doc->original_filename }}</p>
                                    <p class="text-[10px] text-slate-400">Submitted: {{ $doc->updated_at->format('M d, Y h:i A') }}</p>
                                    @if($doc->student_remarks)
                                        <p class="text-[11px] text-slate-600 mt-0.5 italic">Remarks: "{{ $doc->student_remarks }}"</p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button"
                                        @click="openPreview('{{ route('student.compliance.documents.view', $doc->id) }}', '{{ addslashes($req->name) }}', '{{ addslashes($doc->original_filename) }}')"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-300 text-slate-700 text-xs font-bold rounded-lg hover:bg-slate-100 transition shadow-xs cursor-pointer">
                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    View Document
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Upload / Resubmit Form (if not verified) -->
                    @if(!$isVerified)
                        <div class="mt-4 pt-4 border-t border-slate-100" x-data="{ openUpload: {{ (!$doc || $needsCorrection) ? 'true' : 'false' }} }">
                            <div x-show="!openUpload && {{ $doc ? 'true' : 'false' }}">
                                <button type="button" @click="openUpload = true" class="text-xs font-bold text-emerald-700 hover:text-emerald-800 hover:underline">
                                    ↻ Re-upload a different file for this requirement
                                </button>
                            </div>

                            <div x-show="openUpload">
                                <form method="POST" 
                                      action="{{ route('student.compliance.documents.submit', [$complianceRequest->id, $req->id]) }}" 
                                      enctype="multipart/form-data" 
                                      class="space-y-3">
                                    @csrf

                                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                                        <div class="sm:col-span-6">
                                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                                {{ $doc ? 'Select Replacement File (PDF / Image)' : 'Upload File (PDF / Image)' }} <span class="text-red-500">*</span>
                                            </label>
                                            <input type="file" 
                                                   name="document_file" 
                                                   accept=".pdf,.jpg,.jpeg,.png,.webp" 
                                                   required
                                                   class="w-full text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-extrabold file:bg-[#3B060F] file:text-white hover:file:bg-[#6B0F1A] cursor-pointer bg-slate-50 rounded-xl border border-slate-200 p-1">
                                        </div>

                                        <div class="sm:col-span-4">
                                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                                Student Remarks <span class="text-slate-400 font-normal">(Optional)</span>
                                            </label>
                                            <input type="text" 
                                                   name="student_remarks" 
                                                   placeholder="Any note for OSDW reviewer..." 
                                                   class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                                        </div>

                                        <div class="sm:col-span-2">
                                            <button type="submit" 
                                                    class="w-full py-2 px-3 bg-[#3B060F] text-white font-extrabold text-xs rounded-xl hover:bg-[#6B0F1A] transition shadow-md flex items-center justify-center gap-1 cursor-pointer">
                                                <svg class="w-4 h-4 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                                </svg>
                                                {{ $doc ? 'Resubmit' : 'Upload' }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif

                </div>
            @endforeach
        </div>

        <!-- ========================================== -->
        <!-- MODAL IN-BROWSER DOCUMENT PREVIEWER -->
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
                                <p class="text-[11px] text-slate-400">Document Preview (View only)</p>
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
                            <iframe :src="previewUrl" class="w-full h-[70vh] rounded-xl border border-slate-200 bg-white" frameborder="0"></iframe>
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
