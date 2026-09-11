<x-app-layout title="Application #{{ $application->id }} - CSU–Lal-lo">
    
    <div class="max-w-4xl mx-auto space-y-6"
         x-data="{ 
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
         
        <div>
            <a href="{{ route('student.applications.index') }}" class="text-xs font-bold text-csu-green hover:underline inline-flex items-center gap-1 mb-2">
                &larr; Back to My Applications
            </a>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-extrabold text-slate-900">{{ $application->scholarship->name }}</h2>
                    <p class="text-xs text-slate-500">Submitted on: <span class="font-bold text-slate-800">{{ $application->submitted_at ? $application->submitted_at->format('F d, Y \a\t h:i A') : $application->created_at->format('F d, Y') }}</span></p>
                </div>
                <span class="px-4 py-2 rounded-xl text-xs font-extrabold capitalize border 
                    @if($application->status === 'approved') bg-emerald-100 text-emerald-800 border-emerald-300
                    @elseif($application->status === 'rejected') bg-red-100 text-red-800 border-red-300
                    @elseif($application->status === 'incomplete') bg-rose-100 text-rose-800 border-rose-300
                    @elseif($application->status === 'under_review') bg-blue-100 text-blue-800 border-blue-300
                    @else bg-amber-100 text-amber-800 border-amber-300 @endif">
                    Status: {{ str_replace('_', ' ', $application->status) }}
                </span>
            </div>
        </div>

        <!-- Admin Remarks Alert (If any) -->
        @if($application->remarks)
            <div class="p-5 rounded-2xl bg-amber-50 border border-amber-300 text-amber-900 text-xs font-medium space-y-1 shadow-xs">
                <p class="font-extrabold uppercase tracking-wider text-amber-800">Admin Feedback & Remarks:</p>
                <p class="text-slate-800 text-sm font-semibold">{{ $application->remarks }}</p>
            </div>
        @endif

        <!-- Document Verification Status List -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-6 sm:p-8 space-y-6">
            <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Uploaded Application Documents
            </h3>

            <div class="divide-y divide-slate-100">
                @forelse($application->documents as $doc)
                    <div class="py-4 space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <h4 class="text-xs font-extrabold text-slate-900">{{ $doc->requirement->requirement_name ?? 'Document File' }}</h4>
                                <p class="text-[11px] text-slate-400 font-mono mt-0.5">File: {{ $doc->original_filename }}</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <button type="button"
                                        @click="openPreview('{{ route('student.documents.view', $doc->id) }}', '{{ addslashes($doc->requirement->requirement_name ?? 'Uploaded Document') }}', '{{ addslashes($doc->original_filename) }}')"
                                        class="inline-flex items-center gap-1 px-3 py-1 bg-white border border-slate-300 text-slate-700 text-xs font-bold rounded-lg hover:bg-slate-100 transition shadow-xs cursor-pointer">
                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    View File
                                </button>

                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase 
                                    @if($doc->status === 'verified') bg-emerald-100 text-emerald-800 border border-emerald-200
                                    @elseif($doc->status === 'needs_resubmission') bg-rose-100 text-rose-800 border border-rose-200
                                    @else bg-slate-100 text-slate-700 border border-slate-200 @endif">
                                    {{ str_replace('_', ' ', $doc->status) }}
                                </span>
                            </div>
                        </div>

                        <!-- Resubmission Form (If Admin requested resubmission) -->
                        @if($doc->status === 'needs_resubmission' || $application->status === 'incomplete')
                            <div class="bg-rose-50 p-4 rounded-xl border border-rose-200 mt-2 space-y-3">
                                <p class="text-xs text-rose-800 font-bold">Resubmission Requested: <span class="font-medium text-slate-700">{{ $doc->remarks ?? 'Please re-upload a clear file.' }}</span></p>
                                
                                <form method="POST" action="{{ route('student.documents.resubmit', $doc->id) }}" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-center gap-3">
                                    @csrf
                                    <input 
                                        type="file" 
                                        name="document_file" 
                                        required 
                                        accept=".pdf,.jpg,.jpeg,.png"
                                        class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-800 file:text-white"
                                    >
                                    <button type="submit" class="px-4 py-2 bg-rose-600 text-white font-extrabold rounded-xl text-xs hover:bg-rose-700 transition shrink-0">
                                        Re-upload Document
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="py-6 text-center text-xs text-slate-400 italic">No documents attached.</div>
                @endforelse
            </div>
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
                                <p class="text-[11px] text-slate-400">Application Document Preview (View only)</p>
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
