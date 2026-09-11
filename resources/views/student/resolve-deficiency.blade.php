<x-app-layout title="Resolve Deficient Document - CSU Lal-lo OSDW">

    <div class="max-w-2xl mx-auto space-y-6 pb-24">
        
        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-amber-600 via-amber-700 to-amber-800 text-white p-5 rounded-2xl shadow-lg border border-amber-400/30">
            <div class="flex items-center space-x-3">
                <div class="h-10 w-10 rounded-xl bg-white text-amber-800 font-extrabold flex items-center justify-center text-lg shadow-md shrink-0">
                    ⚠️
                </div>
                <div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-white text-amber-900">
                        Action Required
                    </span>
                    <h1 class="text-lg font-extrabold text-white tracking-tight mt-0.5">
                        Deficient Document Resolution Portal
                    </h1>
                    <p class="text-xs text-amber-100">
                        {{ $application->scholarship->name }}
                    </p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('student.applications.resolve.update', $application->id) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            @forelse($application->documents as $doc)
                <!-- Deficient Requirement Item Card -->
                <div class="bg-white rounded-2xl border border-rose-200 shadow-xs p-6 space-y-4">
                    
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center space-x-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-rose-600 animate-pulse"></span>
                            <h3 class="text-sm font-extrabold text-slate-900">
                                {{ $doc->doc_type ?? $doc->requirement->requirement_name ?? 'Flagged Document' }}
                            </h3>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-rose-100 text-rose-800 border border-rose-200">
                            Resubmission Flagged
                        </span>
                    </div>

                    <!-- Evaluator Feedback Card -->
                    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-xs space-y-1">
                        <div class="font-extrabold text-rose-900 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-rose-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                            </svg>
                            OSDW Evaluator Rejection Note:
                        </div>
                        <p class="text-rose-800 font-medium leading-relaxed pl-5">
                            "{{ $doc->remarks ?? 'Document copy requires clearer resubmission or updated copy.' }}"
                        </p>
                    </div>

                    <!-- Re-upload Dropzone -->
                    <div x-data="{ preview: null, fileName: '', fileSize: '' }" class="space-y-3">
                        <label class="block text-xs font-bold text-slate-700">
                            Upload Replacement Document Copy <span class="text-red-500">*</span>
                        </label>

                        <div class="relative">
                            <input type="file" 
                                   name="doc_replace_{{ $doc->id }}" 
                                   id="file_input_{{ $doc->id }}" 
                                   accept="image/*,application/pdf"
                                   capture="environment"
                                   @change="
                                       const file = $event.target.files[0];
                                       if (file) {
                                           fileName = file.name;
                                           fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                                           if (file.type.startsWith('image/')) {
                                               const reader = new FileReader();
                                               reader.onload = (e) => { preview = e.target.result; };
                                               reader.readAsDataURL(file);
                                           } else {
                                               preview = null;
                                           }
                                       }
                                   " 
                                   class="hidden" required>

                            <label for="file_input_{{ $doc->id }}" class="w-full py-3.5 px-4 bg-[#7B1113] hover:bg-[#540B0D] text-white font-extrabold text-xs rounded-xl flex items-center justify-center gap-2 cursor-pointer shadow-md min-h-[48px]">
                                <svg class="w-4 h-4 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                Snap Photo / Choose Replacement File
                            </label>
                        </div>

                        <!-- Instant Client-Side Preview -->
                        <div x-show="fileName" class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex items-center space-x-3 text-xs">
                            <template x-if="preview">
                                <img :src="preview" alt="Replacement Preview" class="h-12 w-12 object-cover rounded-lg border border-slate-300">
                            </template>
                            <div class="overflow-hidden">
                                <div class="font-bold text-slate-900 truncate" x-text="fileName"></div>
                                <div class="text-[10px] text-slate-500" x-text="fileSize"></div>
                            </div>
                        </div>
                    </div>

                </div>
            @empty
                <div class="bg-white rounded-2xl p-8 text-center text-slate-500 border border-slate-200 text-xs">
                    No deficient documents currently pending for this application.
                </div>
            @endforelse

            <!-- Sticky Bottom / Full Width Submit Action Button -->
            <button type="submit" class="w-full py-4 bg-[#7B1113] hover:bg-[#540B0D] text-white font-extrabold text-sm rounded-xl shadow-lg transition flex items-center justify-center gap-2 cursor-pointer min-h-[48px]">
                <svg class="w-5 h-5 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
                Resubmit Deficient Documents to OSDW
            </button>
        </form>

    </div>

</x-app-layout>
