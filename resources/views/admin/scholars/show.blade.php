<x-app-layout title="Scholar Profile - CSU Lal-lo Admin">

    <div x-data="{ docModalOpen: false, activeDocUrl: '', activeDocTitle: '', activeDocType: 'pdf' }">

        <!-- Header & Breadcrumb -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2 text-xs font-semibold text-slate-500 mb-1">
                    <a href="{{ route('admin.scholars.index') }}" class="hover:text-[#6B0F1A] transition">&larr; Scholar Grantees Roster</a>
                    <span>/</span>
                    <span class="text-slate-800">Scholar #{{ $scholar->id }}</span>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                    Scholar Profile: {{ $scholar->student->user->full_name }}
                </h1>
            </div>

            <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-extrabold uppercase tracking-wider
                @if($scholar->status === 'active') bg-emerald-100 text-emerald-800 border border-emerald-300
                @elseif($scholar->status === 'completed') bg-blue-100 text-blue-800 border border-blue-300
                @else bg-rose-100 text-rose-800 border border-rose-300 @endif">
                Status: {{ str_replace('_', ' ', $scholar->status) }}
            </span>
        </div>

        <!-- Main Content Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- LEFT 2 COLUMNS: Profile, Scholarship Details & Submitted Documents -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Student Bio Card -->
                <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-6">
                    <div class="flex items-center space-x-4 border-b border-slate-100 pb-4 mb-4">
                        @if($scholar->student->user->profile_photo_url)
                            <img src="{{ $scholar->student->user->profile_photo_url }}" alt="{{ $scholar->student->user->full_name }}" class="h-14 w-14 rounded-full object-cover border-2 border-[#6B0F1A] shadow-xs shrink-0">
                        @else
                            <div class="h-12 w-12 rounded-full bg-[#3B060F] text-[#FFC107] font-extrabold flex items-center justify-center text-base border border-[#FFC107]/40 shadow-xs shrink-0">
                                {{ strtoupper(substr($scholar->student->user->first_name, 0, 1) . substr($scholar->student->user->last_name, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">
                                Scholar Academic Background
                            </h3>
                            <p class="text-xs text-slate-500 font-mono">{{ $scholar->student->student_number }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Full Name</span>
                            <span class="font-extrabold text-slate-900 text-sm mt-0.5 block">{{ $scholar->student->user->full_name }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Student ID</span>
                            <span class="font-mono font-bold text-slate-800 text-sm mt-0.5 block">{{ $scholar->student->student_number }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Course / Program</span>
                            <span class="font-semibold text-slate-800 text-sm mt-0.5 block">{{ $scholar->student->course }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Year Level</span>
                            <span class="font-semibold text-slate-800 text-sm mt-0.5 block">{{ $scholar->student->year_level }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Email Address</span>
                            <span class="font-semibold text-slate-800 text-sm mt-0.5 block">{{ $scholar->student->user->email }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Contact Number</span>
                            <span class="font-semibold text-slate-800 text-sm mt-0.5 block">{{ $scholar->student->contact_number ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Awarded Scholarship Details -->
                <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-6">
                    <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4">
                        Scholarship Grant Details
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Scholarship Program</span>
                            <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ $scholar->scholarship->name }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Grantor / Provider</span>
                            <span class="font-semibold text-slate-800 text-sm mt-0.5 block">{{ $scholar->scholarship->provider }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Coverage Type</span>
                            <span class="inline-flex items-center gap-1.5 font-bold text-xs mt-0.5 px-2.5 py-1 rounded-lg {{ $scholar->scholarship->isContinuing() ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-blue-50 text-blue-800 border border-blue-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $scholar->scholarship->isContinuing() ? 'bg-emerald-500' : 'bg-blue-500' }}"></span>
                                {{ $scholar->scholarship->coverage_type_label }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Academic Year</span>
                            <span class="font-semibold text-slate-800 text-sm mt-0.5 block">{{ $scholar->scholarship->school_year_label }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Approved Date</span>
                            <span class="font-semibold text-slate-800 text-sm mt-0.5 block">{{ $scholar->approved_at ? $scholar->approved_at->format('F d, Y') : 'N/A' }}</span>
                        </div>
                        <div class="sm:col-span-2">
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Grants & Benefits</span>
                            <p class="font-medium text-slate-800 text-xs mt-1 leading-relaxed bg-slate-50 p-3 rounded-xl border border-slate-200">
                                {{ $scholar->scholarship->benefits }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Submitted Requirements & Documents Card (Displayed Immediately) -->
                <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-4">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">
                                Submitted Requirements & Documents
                            </h3>
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                Official application files and verification documents submitted by this scholar.
                            </p>
                        </div>
                        @if($scholar->application && $scholar->application->documents)
                            <span class="text-xs font-bold text-[#3B060F] bg-emerald-50 px-3 py-1 rounded-lg border border-emerald-200">
                                {{ $scholar->application->documents->where('status', 'verified')->count() }} / {{ $scholar->application->documents->count() }} Verified
                            </span>
                        @endif
                    </div>

                    <div class="space-y-4">
                        @if($scholar->application && $scholar->application->documents && $scholar->application->documents->count() > 0)
                            @foreach($scholar->application->documents as $doc)
                                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="space-y-1">
                                        <div class="flex items-center space-x-2">
                                            <h4 class="text-xs font-bold text-slate-900">
                                                {{ $doc->requirement->requirement_name ?? 'Document Requirement' }}
                                            </h4>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider
                                                @if($doc->status === 'verified') bg-emerald-100 text-emerald-800 border border-emerald-200
                                                @elseif($doc->status === 'needs_resubmission') bg-rose-100 text-rose-800 border border-rose-200
                                                @else bg-amber-100 text-amber-800 border border-amber-200 @endif">
                                                {{ str_replace('_', ' ', $doc->status) }}
                                            </span>
                                        </div>
                                        <p class="text-[11px] text-slate-500 font-mono">
                                            File: {{ $doc->original_filename }}
                                        </p>
                                        @if($doc->remarks)
                                            <p class="text-[11px] text-slate-600 bg-white p-2 rounded-lg border border-slate-200 mt-1">
                                                <span class="font-bold text-slate-700">Remarks:</span> {{ $doc->remarks }}
                                            </p>
                                        @endif
                                    </div>

                                    <!-- Quick In-App View Document Action -->
                                    <button type="button" 
                                            @click="docModalOpen = true; activeDocUrl = '{{ route('admin.applications.documents.download', $doc->id) }}'; activeDocTitle = '{{ addslashes($doc->requirement->requirement_name ?? $doc->original_filename) }}'; activeDocType = '{{ \Illuminate\Support\Str::endsWith(strtolower($doc->original_filename), ['.jpg', '.jpeg', '.png']) ? 'image' : 'pdf' }}'" 
                                            class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-[#3B060F] text-white font-extrabold text-xs rounded-xl hover:bg-[#6B0F1A] transition shadow-xs shrink-0 cursor-pointer">
                                        <svg class="w-4 h-4 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        View Document
                                    </button>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-6 text-slate-500 text-xs">
                                No submitted documents registered for this scholar.
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: Standing Status Update & Navigation Card -->
            <div class="space-y-6">
                
                <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-6 sticky top-6">
                    <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3 mb-4">
                        Scholar Standing Management
                    </h3>

                    <form method="POST" action="{{ route('admin.scholars.update-status', $scholar->id) }}" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Scholarship Standing Status</label>
                            <select name="status" class="w-full py-2 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                                <option value="active" {{ $scholar->status === 'active' ? 'selected' : '' }}>Active Scholar</option>
                                <option value="completed" {{ $scholar->status === 'completed' ? 'selected' : '' }}>Graduated / Program Completed</option>
                                <option value="terminated" {{ $scholar->status === 'terminated' ? 'selected' : '' }}>Terminated / Disqualified</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full py-2.5 bg-[#3B060F] text-white font-extrabold text-xs rounded-xl hover:bg-[#6B0F1A] transition shadow-md cursor-pointer">
                            Update Standing & Notify
                        </button>
                    </form>
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
                            <p class="text-[10px] text-[#FFC107] font-semibold">CSU Lal-lo Scholarship Portal • Scholar In-App Viewer</p>
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

    </div>

</x-app-layout>
