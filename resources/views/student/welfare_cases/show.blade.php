<x-app-layout title="Welfare Case Details - CSU–Lal-lo">
    <div x-data="{
        previewModalOpen: false,
        previewUrl: '',
        previewTitle: 'Document Preview',
        previewType: 'pdf',
        openPreview(url, title, filename) {
            this.previewUrl = url;
            this.previewTitle = title || 'Document Preview';
            const ext = (filename || url).split('.').pop().toLowerCase().split('?')[0];
            this.previewType = ['jpg', 'jpeg', 'png', 'webp', 'gif'].includes(ext) ? 'image' : 'pdf';
            this.previewModalOpen = true;
        }
    }">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('student.welfare-cases.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-900 dark:hover:text-slate-200 transition mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Welfare Cases
                </a>
                <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Case #{{ $welfareCase->case_id }}</h2>
            </div>
            <div>
                @php
                    $statusColors = [
                        'Open' => 'bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-800',
                        'Under Assessment' => 'bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-950/50 dark:text-blue-300 dark:border-blue-800',
                        'Referred' => 'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800',
                        'For Follow-up' => 'bg-purple-50 text-purple-800 border-purple-200 dark:bg-purple-950/50 dark:text-purple-300 dark:border-purple-800',
                        'Resolved' => 'bg-teal-50 text-teal-800 border-teal-200 dark:bg-teal-950/50 dark:text-teal-300 dark:border-teal-800',
                        'Closed' => 'bg-slate-100 text-slate-800 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
                    ];
                    $badgeClass = $statusColors[$welfareCase->status] ?? 'bg-slate-100 text-slate-800 border-slate-200';
                @endphp
                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-black border {{ $badgeClass }}">
                    Status: {{ $welfareCase->status }}
                </span>
            </div>
        </div>

        <!-- Official Referrals Section -->
        @if($welfareCase->referrals->isNotEmpty())
            <div class="mb-6 bg-emerald-50/60 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/60 rounded-2xl p-5 shadow-xs">
                <h3 class="text-emerald-900 dark:text-emerald-200 font-extrabold text-sm flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    OSDW Official Referral & Action
                </h3>
                <p class="text-xs text-emerald-800 dark:text-emerald-300 mb-4">
                    Based on your reported concern, the Office of Student Development and Welfare has reviewed your case and issued an official referral endorsement.
                </p>
                <div class="space-y-3">
                    @foreach($welfareCase->referrals as $referral)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-white dark:bg-slate-900 rounded-xl p-4 border border-emerald-100 dark:border-emerald-800/60 shadow-xs gap-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase {{ $referral->isScholarshipReferral() ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $referral->isScholarshipReferral() ? 'Scholarship Program' : 'University Office' }}
                                    </span>
                                    <h4 class="font-extrabold text-slate-900 dark:text-white text-sm">
                                        {{ $referral->recipient_name }}
                                    </h4>
                                </div>
                                <p class="text-xs text-slate-600 dark:text-slate-300 mt-2">
                                    <span class="font-bold text-slate-700 dark:text-slate-200">OSDW Note:</span> {{ $referral->referral_note }}
                                </p>
                                <p class="text-[10px] text-slate-400 mt-1">Referred on {{ $referral->created_at->format('M d, Y \a\t h:i A') }}</p>
                            </div>
                            <div>
                                @if($referral->isScholarshipReferral() && $referral->scholarship_id)
                                    <a href="{{ route('student.scholarships.show', $referral->scholarship_id) }}" class="inline-flex px-4 py-2 bg-[#3B060F] text-white font-extrabold rounded-xl text-xs hover:bg-[#6B0F1A] transition shrink-0 whitespace-nowrap shadow-xs">
                                        Apply to Scholarship
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2 space-y-6">
                <!-- Case Details -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200 dark:border-slate-800 p-6">
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white mb-4">Concern Details</h3>
                    
                    <div class="mb-4">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Category</p>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700">
                            {{ $welfareCase->category }}
                        </span>
                    </div>
                    
                    <div class="mb-4">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Description</p>
                        <div class="text-xs leading-relaxed text-slate-700 dark:text-slate-200 whitespace-pre-wrap bg-slate-50 dark:bg-slate-800/60 p-4 rounded-xl border border-slate-100 dark:border-slate-800 font-medium">{{ $welfareCase->description }}</div>
                    </div>

                    @if($welfareCase->requested_information)
                        <div class="mb-4">
                            <p class="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider mb-1">Information / Instructions from OSDW</p>
                            <div class="text-xs leading-relaxed text-slate-700 dark:text-slate-200 whitespace-pre-wrap bg-amber-50 dark:bg-amber-950/40 p-4 rounded-xl border border-amber-200 dark:border-amber-800 font-medium">{{ $welfareCase->requested_information }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="space-y-6">
                <!-- Documents -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200 dark:border-slate-800 p-6">
                    <h3 class="text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                        Supporting Documents
                    </h3>
                    
                    @if($welfareCase->documents->isEmpty())
                        <p class="text-xs text-slate-400 italic">No supporting documents uploaded.</p>
                    @else
                        <ul class="space-y-2.5">
                            @foreach($welfareCase->documents as $doc)
                                <li class="flex items-center justify-between gap-3 bg-slate-50 dark:bg-slate-800 p-3 rounded-xl border border-slate-100 dark:border-slate-700">
                                    <div class="flex items-center gap-2.5 overflow-hidden">
                                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                        <p class="text-xs font-semibold text-slate-700 dark:text-slate-200 truncate" title="{{ $doc->file_name }}">{{ $doc->file_name }}</p>
                                    </div>
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <!-- In-app Modal Preview Button -->
                                        <button type="button" 
                                                @click="openPreview('{{ route('student.welfare-cases.documents.view', $doc->id) }}', '{{ addslashes($doc->file_name) }}', '{{ addslashes($doc->file_name) }}')"
                                                class="px-2.5 py-1 bg-[#3B060F] text-white font-extrabold text-[10px] rounded-lg hover:bg-[#6B0F1A] transition inline-flex items-center gap-1 cursor-pointer shadow-xs">
                                            <svg class="w-3 h-3 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            View
                                        </button>
                                        <!-- External Tab Icon -->
                                        <a href="{{ route('student.welfare-cases.documents.view', $doc->id) }}" target="_blank" title="Open in new window" class="p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                        </a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- LARGE IN-APP DOCUMENT PREVIEW MODAL       -->
        <!-- Sized identically to scholar requirements -->
        <!-- ========================================== -->
        <div x-show="previewModalOpen" 
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 overflow-hidden bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-2 sm:p-4"
             @keydown.escape.window="previewModalOpen = false"
             style="display: none;">
            
            <div @click.away="previewModalOpen = false" 
                 class="bg-white dark:bg-slate-900 w-full max-w-5xl xl:max-w-6xl h-[88vh] rounded-2xl shadow-2xl overflow-hidden flex flex-col border border-slate-200 dark:border-slate-800">
                
                <!-- Modal Top Header in CSU Maroon with Title & Explicit Close Button -->
                <div class="px-6 py-4 bg-[#3B060F] text-white flex items-center justify-between border-b border-[#480913] shrink-0">
                    <div class="flex items-center space-x-3 overflow-hidden">
                        <div class="h-9 w-9 rounded-xl bg-white/10 flex items-center justify-center text-[#FFC107] shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="overflow-hidden">
                            <h3 class="text-sm font-extrabold text-white truncate" x-text="previewTitle">Document Viewer</h3>
                            <p class="text-[10px] text-[#FFC107] font-semibold">CSU Lal-lo Student Portal • In-App Document Viewer</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2">
                        <!-- Open in New Tab Button -->
                        <a :href="previewUrl" target="_blank" class="px-3 py-1.5 bg-white/15 hover:bg-white/25 text-white font-bold text-xs rounded-xl transition inline-flex items-center gap-1.5 border border-white/20">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            <span class="hidden sm:inline">New Tab</span>
                        </a>

                        <!-- Explicit Red "Close" Button -->
                        <button @click="previewModalOpen = false" 
                                type="button" 
                                title="Close Viewer (Esc)"
                                class="px-3.5 py-1.5 bg-red-600/90 hover:bg-red-600 text-white text-xs font-extrabold rounded-xl transition flex items-center gap-1.5 shadow-sm border border-red-500/40 cursor-pointer">
                            <span>Close</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Full Viewport Preview Body (Edge-to-Edge with Zero Padding) -->
                <div class="flex-1 bg-slate-900 relative overflow-hidden flex items-center justify-center">
                    <!-- PDF Viewer (Clean Embed without duplicate toolbars) -->
                    <template x-if="previewType === 'pdf'">
                        <iframe :src="previewUrl + '#toolbar=0&navpanes=0'" class="w-full h-full border-0 bg-white" title="PDF Document Viewer"></iframe>
                    </template>

                    <!-- Image Viewer -->
                    <template x-if="previewType === 'image'">
                        <div class="w-full h-full p-4 flex items-center justify-center overflow-auto">
                            <img :src="previewUrl" :alt="previewTitle" class="max-h-full max-w-full object-contain rounded-lg shadow-xl border border-slate-700 select-none pointer-events-none" oncontextmenu="return false;">
                        </div>
                    </template>
                </div>

                <!-- Modal Bottom Action Bar -->
                <div class="px-6 py-3 bg-slate-100 dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between text-xs text-slate-600 dark:text-slate-300 shrink-0">
                    <span class="font-medium flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                        Document View-Only Mode Active
                    </span>
                    <button @click="previewModalOpen = false" type="button" class="px-4 py-1.5 bg-slate-800 dark:bg-slate-700 text-white font-extrabold rounded-xl hover:bg-slate-900 dark:hover:bg-slate-600 transition cursor-pointer">
                        Close Viewer (Esc)
                    </button>
                </div>

            </div>
        </div>

    </div>
</x-app-layout>
