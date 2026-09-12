<x-app-layout title="Apply for Scholarship - CSU Lal-lo OSDW">

    <div x-data="{
        step: 1,
        college: '{{ old('college', $student->college?->value ?? 'CICS') }}',
        program: '{{ old('program', $student->program ?? $student->course ?? '') }}',
        programsByCollege: {
            'CAg': [
                'BS Agriculture (Crop Science)',
                'BS Agriculture (Animal Science)',
                'DAT-BAT (Diploma/Bachelor in Agricultural Tech)'
            ],
            'CHM': [
                'BS Hospitality Management (BSHM)'
            ],
            'CICS': [
                'BS Information Technology (BSIT)'
            ],
            'CTE': [
                'Bachelor of Elementary Education (BEEd)',
                'BSEd - English',
                'BSEd - Mathematics',
                'BSEd - Science',
                'BSEd - Filipino',
                'BSEd - Social Studies'
            ]
        },
        get availablePrograms() {
            return this.programsByCollege[this.college] || [];
        },
        init() {
            if (!this.program && this.availablePrograms.length > 0) {
                this.program = this.availablePrograms[0];
            }
        }
    }" class="pb-24">

        <!-- CSU Institutional Header -->
        <div class="mb-6 bg-gradient-to-r from-[#7B1113] to-[#540B0D] text-white p-5 rounded-2xl shadow-lg border border-[#D4AF37]/30">
            <div class="flex items-center space-x-3">
                <div class="h-10 w-10 rounded-xl bg-[#D4AF37] text-[#7B1113] font-extrabold flex items-center justify-center text-lg shadow-md shrink-0">
                    🎓
                </div>
                <div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-[#D4AF37] text-[#7B1113]">
                        Official Application Portal
                    </span>
                    <h1 class="text-xl font-extrabold text-white tracking-tight mt-0.5">
                        {{ $scholarship->name }}
                    </h1>
                    <p class="text-xs text-slate-200">
                        Provider: {{ $scholarship->provider }} • Coverage: {{ ucfirst($scholarship->coverage_type) }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Progress Tracker Bar -->
        <div class="bg-white rounded-2xl border border-slate-200 p-4 mb-6 shadow-xs">
            <div class="flex items-center justify-between text-xs font-extrabold text-slate-600 mb-2">
                <span :class="{ 'text-[#7B1113] font-extrabold': step === 1 }">1. Academic Info</span>
                <span :class="{ 'text-[#7B1113] font-extrabold': step === 2 }">2. Documents</span>
                <span :class="{ 'text-[#7B1113] font-extrabold': step === 3 }">3. Undertaking</span>
            </div>
            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                <div class="bg-[#7B1113] h-full transition-all duration-300 rounded-full"
                     :style="'width: ' + (step === 1 ? '33.3%' : (step === 2 ? '66.6%' : '100%'))"></div>
            </div>
        </div>

        <form method="POST" action="{{ route('student.applications.store', $scholarship->id) }}" enctype="multipart/form-data">
            @csrf

            <!-- STEP 1: Academic & Demographic Confirmation -->
            <div x-show="step === 1" x-transition class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-5">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="h-6 w-6 rounded-full bg-[#7B1113] text-white flex items-center justify-center text-xs">1</span>
                        Academic & Demographic Details
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">Verify your student background for eligibility rules calculation.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <!-- Student Number -->
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Student ID Number</label>
                        <input type="text" value="{{ $student->student_number }}" class="w-full py-2.5 px-3 bg-slate-100 border border-slate-300 rounded-xl font-mono text-slate-700" readonly>
                    </div>

                    <!-- Full Name -->
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Full Student Name</label>
                        <input type="text" value="{{ $student->user->full_name }}" class="w-full py-2.5 px-3 bg-slate-100 border border-slate-300 rounded-xl font-bold text-slate-800" readonly>
                    </div>

                    <!-- College Select -->
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">CSU Lal-lo College <span class="text-red-500">*</span></label>
                        <select name="college" x-model="college" @change="program = availablePrograms[0]" class="w-full py-2.5 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold focus:ring-2 focus:ring-[#7B1113]">
                            <option value="CAg">CAg - College of Agriculture</option>
                            <option value="CHM">CHM - College of Hospitality Management</option>
                            <option value="CICS">CICS - College of Information & Computing Sciences</option>
                            <option value="CTE">CTE - College of Teacher Education</option>
                        </select>
                    </div>

                    <!-- Degree Program Select -->
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Degree Program <span class="text-red-500">*</span></label>
                        <select name="program" x-model="program" class="w-full py-2.5 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold focus:ring-2 focus:ring-[#7B1113]">
                            <template x-for="p in availablePrograms" :key="p">
                                <option :value="p" x-text="p"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Year Level -->
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Year Level <span class="text-red-500">*</span></label>
                        <select name="year_level" class="w-full py-2.5 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold focus:ring-2 focus:ring-[#7B1113]">
                            <option value="1st Year" {{ old('year_level', $student->year_level) === '1st Year' ? 'selected' : '' }}>1st Year</option>
                            <option value="2nd Year" {{ old('year_level', $student->year_level) === '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                            <option value="3rd Year" {{ old('year_level', $student->year_level) === '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                            <option value="4th Year" {{ old('year_level', $student->year_level) === '4th Year' ? 'selected' : '' }}>4th Year</option>
                        </select>
                    </div>

                    <!-- Verified GWA Input -->
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Current General Weighted Average (GWA) <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" min="1.00" max="5.00" inputmode="decimal" name="current_gwa" value="{{ old('current_gwa', $student->current_gwa) }}" placeholder="e.g. 1.75" class="w-full py-2.5 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono font-bold text-slate-900 focus:ring-2 focus:ring-[#7B1113]" required>
                        @if($scholarship->min_gwa)
                            <span class="text-[11px] text-amber-700 font-bold mt-1 block">Cutoff Requirement: GWA ≤ {{ number_format($scholarship->min_gwa, 2) }}</span>
                        @endif
                    </div>

                    <!-- Monthly Household Income -->
                    <div>
                        <label class="block text-slate-700 font-bold mb-1">Monthly Household Income (₱) <span class="text-red-500">*</span></label>
                        <input type="number" step="100" inputmode="numeric" name="monthly_household_income" value="{{ old('monthly_household_income', $student->monthly_household_income) }}" placeholder="e.g. 15000" class="w-full py-2.5 px-3 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-[#7B1113]" required>
                    </div>

                    <!-- 4Ps Beneficiary Toggle -->
                    <div class="flex items-center space-x-3 pt-6 sm:pt-4">
                        <input type="checkbox" name="is_4ps" id="is_4ps" value="1" {{ old('is_4ps', $student->is_4ps) ? 'checked' : '' }} class="h-5 w-5 text-[#7B1113] rounded border-slate-300 focus:ring-[#7B1113] cursor-pointer">
                        <label for="is_4ps" class="text-xs font-bold text-slate-800 cursor-pointer">
                            Household is a 4Ps Beneficiary
                        </label>
                    </div>
                </div>
            </div>

            <!-- STEP 2: Document Checklist & Upload Slots -->
            <div x-show="step === 2" x-transition class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-5">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="h-6 w-6 rounded-full bg-[#7B1113] text-white flex items-center justify-center text-xs">2</span>
                        Required Document Checklist
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">Upload clear photos or PDF scans of your official university requirements.</p>
                </div>

                <div class="space-y-4">
                    @forelse($scholarship->requirements->where('requirement_type', 'document') as $req)
                        <div x-data="{ preview: null, fileName: '', fileSize: '' }" class="p-4 rounded-xl border border-slate-200 bg-slate-50/60 space-y-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-xs font-extrabold text-slate-900">{{ $req->requirement_name }}</h4>
                                    <p class="text-[11px] text-slate-500">{{ $req->description ?? 'Official copy required for evaluation.' }}</p>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $req->is_required ? 'bg-red-100 text-red-800' : 'bg-slate-200 text-slate-700' }}">
                                    {{ $req->is_required ? 'Mandatory' : 'Optional' }}
                                </span>
                            </div>

                            <!-- Mobile Camera & File Selection Controls -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 items-center">
                                <div class="relative">
                                    <input type="file" 
                                           name="doc_{{ $req->id }}" 
                                           id="doc_input_{{ $req->id }}" 
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
                                           class="hidden" {{ $req->is_required ? 'required' : '' }}>

                                    <label for="doc_input_{{ $req->id }}" class="w-full py-3 px-4 bg-[#7B1113] hover:bg-[#540B0D] text-white font-extrabold text-xs rounded-xl flex items-center justify-center gap-2 cursor-pointer shadow-xs min-h-[48px]">
                                        <svg class="w-4 h-4 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        </svg>
                                        Take Photo / Upload Document
                                    </label>
                                </div>

                                <!-- Thumbnail & File Status Indicator -->
                                <div x-show="fileName" class="p-2 bg-white rounded-lg border border-slate-200 flex items-center space-x-3 text-xs">
                                    <template x-if="preview">
                                        <img :src="preview" alt="Preview" class="h-10 w-10 object-cover rounded-md border border-slate-300">
                                    </template>
                                    <div class="overflow-hidden">
                                        <div class="font-bold text-slate-800 truncate" x-text="fileName"></div>
                                        <div class="text-[10px] text-slate-500" x-text="fileSize"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-500 text-xs">
                            No specific document requirements registered for this grant.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- STEP 3: Anti-Duplication Undertaking & Final Submit -->
            <div x-show="step === 3" x-transition class="bg-white rounded-2xl border border-slate-200 p-6 shadow-xs space-y-6">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="h-6 w-6 rounded-full bg-[#7B1113] text-white flex items-center justify-center text-xs">3</span>
                        Application Review & Legal Undertaking
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">Review your entered credentials and sign the anti-duplication agreement.</p>
                </div>

                <!-- Summary Snapshot Card -->
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 text-xs space-y-2">
                    <div class="font-extrabold text-[#7B1113] text-sm mb-1">Application Summary</div>
                    <div class="grid grid-cols-2 gap-2">
                        <div><strong>College:</strong> <span x-text="college"></span></div>
                        <div><strong>Program:</strong> <span x-text="program"></span></div>
                    </div>
                </div>

                <!-- Anti-Duplication Undertaking Accordion & Checkbox -->
                <div class="p-4 rounded-xl border border-amber-200 bg-amber-50/70 text-xs space-y-3">
                    <div class="font-extrabold text-amber-900 flex items-center gap-1.5 text-sm">
                        <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Mandatory Anti-Duplication Legal Undertaking
                    </div>
                    <p class="text-amber-800 leading-relaxed text-[11px]">
                        I hereby certify that all information provided and documents attached are true, correct, and authentic. I solemnly affirm that I am not receiving any other mutually exclusive government scholarship grant for the same coverage period.
                    </p>

                    <div class="flex items-start space-x-3 pt-2">
                        <input type="checkbox" name="legal_undertaking" id="legal_undertaking" value="1" class="h-5 w-5 text-[#7B1113] rounded border-slate-300 focus:ring-[#7B1113] cursor-pointer mt-0.5" required>
                        <label for="legal_undertaking" class="text-xs font-extrabold text-slate-900 cursor-pointer">
                            I agree to the CSU Lal-lo OSDW Anti-Duplication Rules & Guidelines <span class="text-red-600">*</span>
                        </label>
                    </div>
                </div>

                <!-- Final Submit Trigger Button -->
                <button type="submit" class="w-full py-3.5 bg-[#7B1113] hover:bg-[#540B0D] text-white font-extrabold text-sm rounded-xl shadow-lg transition flex items-center justify-center gap-2 cursor-pointer min-h-[48px]">
                    <svg class="w-5 h-5 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                    Submit Official Application
                </button>
            </div>

            <!-- Sticky Bottom Mobile Thumb-Zone Navigation Bar -->
            <div class="fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-slate-200 px-4 py-3 shadow-2xl flex items-center justify-between h-16 sm:hidden">
                <button type="button" 
                        x-show="step > 1" 
                        @click="step--" 
                        class="px-4 py-2.5 bg-slate-200 text-slate-800 font-extrabold text-xs rounded-xl cursor-pointer min-h-[48px] flex items-center">
                    &larr; Back
                </button>

                <button type="button" 
                        x-show="step < 3" 
                        @click="step++" 
                        class="ml-auto px-6 py-2.5 bg-[#7B1113] text-white font-extrabold text-xs rounded-xl cursor-pointer shadow-md min-h-[48px] flex items-center gap-1.5">
                    Next Step &rarr;
                </button>
            </div>
        </form>

    </div>

</x-app-layout>
