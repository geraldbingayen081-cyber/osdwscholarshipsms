<x-app-layout title="Report Welfare Concern - CSU–Lal-lo">
    
    <div class="mb-6">
        <a href="{{ route('student.welfare-cases.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-900 dark:hover:text-slate-200 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Welfare Cases
        </a>
    </div>

    <div class="mb-6">
        <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Report a Student Welfare Concern</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">
            The Office of Student Development and Welfare (OSDW) is here to assist you with financial, academic, personal, health, and campus life concerns.
        </p>
    </div>

    <div x-data="{
        category: '{{ old('category', \App\Models\WelfareCase::CATEGORY_SCHOLARSHIP) }}',
        documents: [{ id: 1 }],
        get placeholderText() {
            switch(this.category) {
                case '{{ \App\Models\WelfareCase::CATEGORY_SCHOLARSHIP }}':
                    return 'Describe your financial need (e.g. tuition support, school materials/practicum, daily transportation allowance, calamity relief) and household circumstances...';
                case '{{ \App\Models\WelfareCase::CATEGORY_ASSISTANTSHIP }}':
                    return 'State your college, year level, current GWA, preferred department or campus office for assignment, and available hours/schedule...';
                case '{{ \App\Models\WelfareCase::CATEGORY_SMAP }}':
                    return 'Describe the medical illness, accident, hospitalization, surgical need, or mutual aid benefit being requested. Please attach medical certificate or hospital records if available...';
                case '{{ \App\Models\WelfareCase::CATEGORY_CO_CURRICULAR }}':
                    return 'Provide details of the competition or event: name, level (campus, regional, national, international), awards/honors received, date, and organizing body...';
                case '{{ \App\Models\WelfareCase::CATEGORY_TOPNOTCHERS }}':
                    return 'Specify the Professional Regulation Commission (PRC) or Bar examination taken, rank/placing achieved, examination date, and rating...';
                case '{{ \App\Models\WelfareCase::CATEGORY_ORG_ACCREDITATION }}':
                    return 'Specify the name of the student organization, category (academic, socio-cultural, religious, special interest), officer roster status, and submitted constitution/by-laws...';
                case '{{ \App\Models\WelfareCase::CATEGORY_ORG_ADVISING }}':
                    return 'Describe the specific consultation, activity clearance, adviser appointment, event permit, or technical advising needed for your recognized student organization...';
                default:
                    return 'Please describe your request or concern in detail so OSDW can properly evaluate and assist you...';
            }
        }
    }" class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200 dark:border-slate-800 overflow-hidden max-w-3xl">

        <form action="{{ route('student.welfare-cases.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf

            <!-- Category Selection -->
            <div>
                <label for="category" class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                    Service / Welfare Category <span class="text-red-500">*</span>
                </label>
                <select name="category" 
                        id="category" 
                        x-model="category"
                        required 
                        class="w-full py-2.5 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-[#3B060F] outline-none transition">
                    @foreach(\App\Models\WelfareCase::CATEGORIES as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
                @error('category') <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p> @enderror
            </div>

            <!-- Dynamic Category Guidance Banner -->
            <div class="p-4 rounded-xl border text-xs leading-relaxed transition-all"
                 :class="{
                    'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200': category === '{{ \App\Models\WelfareCase::CATEGORY_SCHOLARSHIP }}',
                    'bg-blue-50 dark:bg-blue-950/40 border-blue-200 dark:border-blue-800 text-blue-900 dark:text-blue-200': category === '{{ \App\Models\WelfareCase::CATEGORY_ASSISTANTSHIP }}',
                    'bg-rose-50 dark:bg-rose-950/40 border-rose-200 dark:border-rose-800 text-rose-900 dark:text-rose-200': category === '{{ \App\Models\WelfareCase::CATEGORY_SMAP }}',
                    'bg-purple-50 dark:bg-purple-950/40 border-purple-200 dark:border-purple-800 text-purple-900 dark:text-purple-200': category === '{{ \App\Models\WelfareCase::CATEGORY_CO_CURRICULAR }}',
                    'bg-amber-50 dark:bg-amber-950/40 border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-200': category === '{{ \App\Models\WelfareCase::CATEGORY_TOPNOTCHERS }}',
                    'bg-cyan-50 dark:bg-cyan-950/40 border-cyan-200 dark:border-cyan-800 text-cyan-900 dark:text-cyan-200': category === '{{ \App\Models\WelfareCase::CATEGORY_ORG_ACCREDITATION }}',
                    'bg-indigo-50 dark:bg-indigo-950/40 border-indigo-200 dark:border-indigo-800 text-indigo-900 dark:text-indigo-200': category === '{{ \App\Models\WelfareCase::CATEGORY_ORG_ADVISING }}'
                 }">
                
                <template x-if="category === '{{ \App\Models\WelfareCase::CATEGORY_SCHOLARSHIP }}'">
                    <div class="space-y-1">
                        <p class="font-extrabold flex items-center gap-1.5">
                            <span>🎓</span> Scholarship and Financial Assistance
                        </p>
                        <p class="text-[11px] opacity-90">
                            OSDW evaluates cases for institutional financial aid and official endorsements to active <strong>Scholarship Programs</strong>. Uploading your Certificate of Indigency, payslips, or utility bills helps expedite evaluation.
                        </p>
                    </div>
                </template>

                <template x-if="category === '{{ \App\Models\WelfareCase::CATEGORY_ASSISTANTSHIP }}'">
                    <div class="space-y-1">
                        <p class="font-extrabold flex items-center gap-1.5">
                            <span>💼</span> Student Assistantship Program
                        </p>
                        <p class="text-[11px] opacity-90">
                            Deserving students may be placed in academic or administrative departments as Student Assistants. Please attach your Certificate of Registration (COR) and official semester class schedule.
                        </p>
                    </div>
                </template>

                <template x-if="category === '{{ \App\Models\WelfareCase::CATEGORY_SMAP }}'">
                    <div class="space-y-1">
                        <p class="font-extrabold flex items-center gap-1.5">
                            <span>🛡️</span> Student Mutual Aid Program (SMAP)
                        </p>
                        <p class="text-[11px] opacity-90">
                            Provides emergency assistance for student illness, hospitalization, accidental injury, or bereavement support. Please attach physician medical certificates, diagnostic laboratory reports, or hospital billing statements.
                        </p>
                    </div>
                </template>

                <template x-if="category === '{{ \App\Models\WelfareCase::CATEGORY_CO_CURRICULAR }}'">
                    <div class="space-y-1">
                        <p class="font-extrabold flex items-center gap-1.5">
                            <span>🏆</span> Co-Curricular Programs Incentives
                        </p>
                        <p class="text-[11px] opacity-90">
                            Incentives and institutional recognition for students excelling in recognized regional, national, or international athletic, socio-cultural, and academic competitions. Attach proof of certificate or event results.
                        </p>
                    </div>
                </template>

                <template x-if="category === '{{ \App\Models\WelfareCase::CATEGORY_TOPNOTCHERS }}'">
                    <div class="space-y-1">
                        <p class="font-extrabold flex items-center gap-1.5">
                            <span>⭐</span> Incentives for National Topnotchers in Board Examinations
                        </p>
                        <p class="text-[11px] opacity-90">
                            Official cash incentives and citations for CSU Lal-lo graduates achieving Top 10 rank in PRC Licensure Examinations. Attach copy of PRC Board Rating and official PRC press release.
                        </p>
                    </div>
                </template>

                <template x-if="category === '{{ \App\Models\WelfareCase::CATEGORY_ORG_ACCREDITATION }}'">
                    <div class="space-y-1">
                        <p class="font-extrabold flex items-center gap-1.5">
                            <span>🏛️</span> Student Organizations Accreditation / Re-Accreditation
                        </p>
                        <p class="text-[11px] opacity-90">
                            Official recognition application for campus student organizations. Please upload your Constitution and By-Laws, List of Officers, Action Plan, and Faculty Adviser Acceptance Form.
                        </p>
                    </div>
                </template>

                <template x-if="category === '{{ \App\Models\WelfareCase::CATEGORY_ORG_ADVISING }}'">
                    <div class="space-y-1">
                        <p class="font-extrabold flex items-center gap-1.5">
                            <span>🧭</span> Advising of Student Organizations
                        </p>
                        <p class="text-[11px] opacity-90">
                            Consultation, activity clearance endorsements, leadership advising, and conflict resolution managed by the Student Organizations & Activities Unit (SOAU).
                        </p>
                    </div>
                </template>
            </div>

            <!-- Description Textarea -->
            <div>
                <label for="description" class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                    Concern Details <span class="text-red-500">*</span>
                </label>
                <textarea name="description" 
                          id="description" 
                          rows="6" 
                          required 
                          :placeholder="placeholderText" 
                          class="w-full p-3.5 bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-[#3B060F] outline-none transition leading-relaxed">{{ old('description') }}</textarea>
                @error('description') <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p> @enderror
            </div>

            <!-- Dynamic Supporting Documents (Optional) -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                        Supporting Documents (Optional)
                    </label>
                    <button type="button" 
                            @click="documents.push({ id: documents.length + 1 })" 
                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-[#3B060F] text-white font-extrabold rounded-lg text-xs hover:bg-[#6B0F1A] transition cursor-pointer shadow-xs">
                        <svg class="w-3.5 h-3.5 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Another File
                    </button>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mb-3 font-medium">
                    Upload any supporting files relevant to your concern (e.g. Indigency slips, medical records, incident reports, photos). Supported formats: PDF, JPG, PNG (Max 5MB each).
                </p>

                <div class="space-y-2.5">
                    <template x-for="(doc, index) in documents" :key="doc.id">
                        <div class="flex items-center gap-3 bg-slate-50 dark:bg-slate-800 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700">
                            <input type="file" 
                                   name="documents[]" 
                                   accept=".pdf,.jpg,.jpeg,.png" 
                                   class="flex-1 text-xs text-slate-600 dark:text-slate-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-[#FFC107] file:text-[#3B060F] hover:file:bg-amber-400 transition cursor-pointer" />
                            <button type="button" 
                                    @click="if(documents.length > 1) documents.splice(index, 1)" 
                                    x-show="documents.length > 1" 
                                    class="text-rose-500 hover:text-rose-700 p-1.5 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950 transition cursor-pointer" 
                                    title="Remove this file">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>
                @error('documents.*') <p class="mt-1 text-xs text-rose-500 font-semibold">{{ $message }}</p> @enderror
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-3">
                <a href="{{ route('student.welfare-cases.index') }}" 
                   class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 bg-[#3B060F] hover:bg-[#6B0F1A] text-white font-extrabold text-xs rounded-xl transition shadow-md cursor-pointer flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Submit Welfare Case
                </button>
            </div>
        </form>
    </div>

</x-app-layout>
