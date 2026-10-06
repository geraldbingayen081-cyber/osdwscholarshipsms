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

    $defaultOffice = match($welfareCase->category) {
        'Guidance and Counseling' => 'University Guidance & Counseling Center',
        'Medical / Health Concern' => 'Campus Clinic & Health Services',
        'Academic Grievance' => 'College Dean / Department Chairperson',
        'Student Conduct / Discipline' => 'Student Disciplinary Tribunal / Prefect of Discipline',
        'Housing / Dormitory' => 'Campus Dormitory / Housing Office',
        default => 'OSDW Student Welfare Division'
    };
    $defaultReferralType = $welfareCase->category === 'Financial Assistance' ? 'scholarship' : 'office';
@endphp

<x-app-layout title="Welfare Case #{{ $welfareCase->case_id }} - CSU Lal-lo Admin">

    <div x-data="{
        previewModal: false,
        previewUrl: '',
        previewName: '',
        isPdf: false,
        openReferralModal: false,
        referralType: '{{ $defaultReferralType }}',
        selectedOffice: '{{ $defaultOffice }}',
        openPreview(url, name) {
            this.previewUrl = url;
            this.previewName = name;
            this.isPdf = name.toLowerCase().endsWith('.pdf');
            this.previewModal = true;
        }
    }">

        <!-- Back Navigation & Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('admin.welfare-cases.index') }}" 
                   class="inline-flex items-center gap-1 text-xs font-bold text-slate-500 hover:text-[#3B060F] dark:hover:text-[#FFC107] transition mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Welfare Cases
                </a>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        Welfare Case #{{ $welfareCase->case_id }}
                    </h1>
                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-black border {{ $badgeClass }}">
                        {{ $welfareCase->status }}
                    </span>
                </div>
            </div>

            <!-- Action Button: Refer to Scholarship / Office -->
            @if(in_array($welfareCase->status, ['Open', 'Under Assessment']))
                <div>
                    <button type="button"
                            @click="openReferralModal = true" 
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold text-xs rounded-xl transition shadow-md cursor-pointer">
                        <svg class="w-4 h-4 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        </svg>
                        Issue Official Referral / Action
                    </button>
                </div>
            @endif
        </div>

        <!-- Referral Modal -->
        @if(in_array($welfareCase->status, ['Open', 'Under Assessment']))
            <div x-show="openReferralModal" 
                 x-cloak
                 class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
                <div @click.away="openReferralModal = false" 
                     class="bg-white dark:bg-slate-900 rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800">
                    <div class="flex items-center justify-between mb-4 border-b border-slate-100 dark:border-slate-800 pb-3">
                        <h3 class="font-extrabold text-slate-900 dark:text-white text-base flex items-center gap-2">
                            <span class="h-7 w-7 rounded-lg bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 flex items-center justify-center text-xs font-bold">
                                ★
                            </span>
                            Official Case Referral
                        </h3>
                        <button type="button" @click="openReferralModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('admin.welfare-cases.refer', $welfareCase->id) }}" class="space-y-4">
                        @csrf

                        <!-- Referral Type Selector (Scholarship vs University Office) -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">
                                Referral Channel <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-2 bg-slate-100 dark:bg-slate-800 p-1 rounded-xl">
                                <button type="button" 
                                        @click="referralType = 'scholarship'" 
                                        :class="referralType === 'scholarship' ? 'bg-[#3B060F] text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900'"
                                        class="py-2 text-xs font-extrabold rounded-lg transition text-center cursor-pointer">
                                    Scholarship Program
                                </button>
                                <button type="button" 
                                        @click="referralType = 'office'" 
                                        :class="referralType === 'office' ? 'bg-[#3B060F] text-white shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900'"
                                        class="py-2 text-xs font-extrabold rounded-lg transition text-center cursor-pointer">
                                    University Office / Dept
                                </button>
                            </div>
                            <input type="hidden" name="referral_type" :value="referralType">
                        </div>

                        <!-- Target Scholarship (When type is scholarship) -->
                        <div x-show="referralType === 'scholarship'">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Target Scholarship Program <span class="text-red-500">*</span>
                            </label>
                            <select name="scholarship_id" 
                                    :disabled="referralType !== 'scholarship'"
                                    :required="referralType === 'scholarship'"
                                    class="w-full py-2.5 px-3.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-[#3B060F] outline-none">
                                <option value="">-- Select Scholarship Program --</option>
                                @foreach($availableScholarships as $scholarship)
                                    <option value="{{ $scholarship->id }}">{{ $scholarship->name }} ({{ $scholarship->coverage_type ?? 'Full' }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Target Office (When type is office) -->
                        <div x-show="referralType === 'office'">
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Referred University Office / Department <span class="text-red-500">*</span>
                            </label>
                            <select name="referred_to_office" 
                                    x-model="selectedOffice"
                                    :disabled="referralType !== 'office'"
                                    :required="referralType === 'office'"
                                    class="w-full py-2.5 px-3.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-[#3B060F] outline-none">
                                <option value="University Guidance & Counseling Center">University Guidance & Counseling Center</option>
                                <option value="Campus Clinic & Health Services">Campus Clinic & Health Services</option>
                                <option value="College Dean / Department Chairperson">College Dean / Department Chairperson</option>
                                <option value="Student Disciplinary Tribunal / Prefect of Discipline">Student Disciplinary Tribunal / Prefect of Discipline</option>
                                <option value="Campus Dormitory / Housing Office">Campus Dormitory / Housing Office</option>
                                <option value="OSDW Student Welfare Division">OSDW Student Welfare Division</option>
                                <option value="Office of Academic Affairs">Office of Academic Affairs</option>
                            </select>
                        </div>

                        <!-- Referral Notes -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Official Referral Endorsement & Next Steps <span class="text-red-500">*</span>
                            </label>
                            <textarea name="referral_note" 
                                      rows="4" 
                                      required 
                                      placeholder="Enter detailed endorsement notes, scheduled session instructions, or actions required from the receiving unit / student..."
                                      class="w-full p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-[#3B060F] outline-none"></textarea>
                            <p class="text-[11px] text-slate-400 mt-1">
                                The student and receiving unit will receive notification and record of this referral.
                            </p>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                            <button type="button" @click="openReferralModal = false" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl hover:bg-slate-200 transition cursor-pointer">
                                Cancel
                            </button>
                            <button type="submit" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold text-xs rounded-xl transition shadow-xs cursor-pointer">
                                Confirm Referral
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- Main Grid Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left Column: Case Details & Documents (2 Columns) -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Case Concern Card -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-xs">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#3B060F] dark:text-[#FFC107]">Category</span>
                            <h2 class="text-base font-extrabold text-slate-900 dark:text-white mt-0.5">{{ $welfareCase->category }}</h2>
                        </div>
                        <span class="text-xs text-slate-400 font-medium">
                            Reported on {{ $welfareCase->created_at->format('F d, Y \a\t h:i A') }}
                        </span>
                    </div>

                    <div class="mt-4">
                        <h3 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Student Concern Description</h3>
                        <div class="bg-slate-50 dark:bg-slate-800/60 p-4 rounded-xl text-xs leading-relaxed text-slate-800 dark:text-slate-200 font-medium whitespace-pre-line border border-slate-100 dark:border-slate-800">
                            {{ $welfareCase->description }}
                        </div>
                    </div>

                    <!-- Submitted Supporting Documents with INLINE PREVIEW -->
                    <div class="mt-6 pt-5 border-t border-slate-100 dark:border-slate-800">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                                Submitted Supporting Documents ({{ $welfareCase->documents->count() }})
                            </h3>
                            <span class="text-[11px] text-slate-400">Click View to preview without downloading</span>
                        </div>

                        @if($welfareCase->documents->count() > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach($welfareCase->documents as $doc)
                                    @php
                                        $ext = strtolower(pathinfo($doc->file_name, PATHINFO_EXTENSION));
                                        $isPdf = in_array($ext, ['pdf']);
                                    @endphp
                                    <div class="p-3 bg-slate-50 dark:bg-slate-800/80 rounded-xl border border-slate-200 dark:border-slate-700 flex flex-col justify-between space-y-3 hover:border-slate-300 dark:hover:border-slate-600 transition">
                                        <div class="flex items-center space-x-2.5 overflow-hidden">
                                            <div class="h-9 w-9 rounded-lg {{ $isPdf ? 'bg-red-100 text-red-700 dark:bg-red-950/60 dark:text-red-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-950/60 dark:text-blue-400' }} flex items-center justify-center shrink-0 text-xs font-extrabold uppercase">
                                                {{ $ext ?: 'DOC' }}
                                            </div>
                                            <div class="overflow-hidden">
                                                <p class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate" title="{{ $doc->file_name }}">
                                                    {{ $doc->file_name }}
                                                </p>
                                                <span class="text-[10px] text-slate-400 block">Uploaded {{ $doc->created_at->format('M d, Y') }}</span>
                                            </div>
                                        </div>

                                        <!-- Actions: View (Modal Preview) & Download -->
                                        <div class="flex items-center gap-2 pt-2 border-t border-slate-200/60 dark:border-slate-700/60">
                                            <!-- View Inline Button -->
                                            <button type="button" 
                                                    @click="openPreview('{{ route('admin.welfare-cases.documents.view', $doc->id) }}', '{{ addslashes($doc->file_name) }}')"
                                                    class="flex-1 py-1.5 px-3 bg-[#3B060F] hover:bg-[#6B0F1A] text-white text-[11px] font-extrabold rounded-lg transition shadow-xs inline-flex items-center justify-center gap-1.5 cursor-pointer">
                                                <svg class="w-3.5 h-3.5 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                View Document
                                            </button>

                                            <!-- Download fallback -->
                                            <a href="{{ route('admin.welfare-cases.documents.download', $doc->id) }}" 
                                               title="Download file"
                                               class="p-1.5 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-300 dark:hover:bg-slate-600 rounded-lg transition shrink-0 inline-flex items-center justify-center">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-slate-400 italic">No supporting documents uploaded for this case.</p>
                        @endif
                    </div>
                </div>

                <!-- Existing Referrals History Card -->
                @if($welfareCase->referrals->count() > 0)
                    <div class="bg-emerald-50/50 dark:bg-emerald-950/20 rounded-2xl p-6 border border-emerald-200 dark:border-emerald-800/60 shadow-xs">
                        <h3 class="text-xs font-extrabold text-emerald-800 dark:text-emerald-300 uppercase tracking-wider mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Official Case Referral Endorsements
                        </h3>

                        <div class="space-y-3">
                            @foreach($welfareCase->referrals as $ref)
                                <div class="p-4 bg-white dark:bg-slate-900 rounded-xl border border-emerald-200 dark:border-emerald-800 text-xs">
                                    <div class="flex items-center justify-between mb-1.5">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase {{ $ref->isScholarshipReferral() ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800' }}">
                                                {{ $ref->isScholarshipReferral() ? 'Scholarship Program' : 'University Department' }}
                                            </span>
                                            <span class="font-extrabold text-slate-900 dark:text-white text-sm">
                                                {{ $ref->recipient_name }}
                                            </span>
                                        </div>
                                        <span class="text-[10px] text-slate-400">{{ $ref->created_at->format('M d, Y h:i A') }}</span>
                                    </div>
                                    <p class="text-slate-600 dark:text-slate-300 mt-1">
                                        <strong class="text-slate-700 dark:text-slate-200">OSDW Staff Note:</strong> {{ $ref->referral_note }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Related Student Records: Active Grants & Previous Applications -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-xs">
                    <h3 class="text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-4">
                        Related Student Scholarship Records
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Active Grants -->
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Active Grants</span>
                            <div class="mt-2 space-y-2">
                                @forelse($welfareCase->student->scholars->where('status', 'active') as $scholar)
                                    <div class="p-2.5 bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-700 text-xs flex items-center justify-between">
                                        <span class="font-bold text-slate-800 dark:text-slate-200 truncate">{{ $scholar->scholarship->name }}</span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-100 text-emerald-800">Active</span>
                                    </div>
                                @empty
                                    <p class="text-xs text-slate-400 italic">No active scholarship grants.</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Past Applications -->
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Past Applications</span>
                            <div class="mt-2 space-y-2">
                                @forelse($welfareCase->student->applications->take(3) as $app)
                                    <div class="p-2.5 bg-white dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-700 text-xs flex items-center justify-between">
                                        <span class="font-bold text-slate-800 dark:text-slate-200 truncate">{{ $app->scholarship->name }}</span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-slate-100 text-slate-700">
                                            {{ str_replace('_', ' ', $app->status) }}
                                        </span>
                                    </div>
                                @empty
                                    <p class="text-xs text-slate-400 italic">No previous applications on record.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Student Profile & Assessment Update Form (1 Column) -->
            <div class="space-y-6">

                <!-- Student Profile Summary -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-xs">
                    <h3 class="text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-4">
                        Student Profile
                    </h3>

                    <div class="flex items-center space-x-3 mb-4 pb-4 border-b border-slate-100 dark:border-slate-800">
                        <div class="h-12 w-12 rounded-2xl bg-[#3B060F] text-[#FFC107] font-extrabold text-sm flex items-center justify-center shrink-0 shadow-xs">
                            {{ strtoupper(substr($welfareCase->student->user->first_name ?? 'S', 0, 1) . substr($welfareCase->student->user->last_name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="overflow-hidden">
                            <p class="font-bold text-sm text-slate-900 dark:text-white truncate">
                                {{ $welfareCase->student->user->full_name ?? 'Student' }}
                            </p>
                            <p class="text-xs text-slate-400 truncate">{{ $welfareCase->student->user->email ?? 'No email' }}</p>
                        </div>
                    </div>

                    <div class="space-y-2.5 text-xs">
                        <div class="flex justify-between py-1 border-b border-slate-50 dark:border-slate-800/40">
                            <span class="text-slate-400">Student Number:</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $welfareCase->student->student_number ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50 dark:border-slate-800/40">
                            <span class="text-slate-400">Course / Program:</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $welfareCase->student->course ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50 dark:border-slate-800/40">
                            <span class="text-slate-400">Year Level:</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ $welfareCase->student->year_level ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50 dark:border-slate-800/40">
                            <span class="text-slate-400">Current GWA:</span>
                            <span class="font-bold text-emerald-700 dark:text-emerald-400">{{ number_format($welfareCase->student->current_gwa ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span class="text-slate-400">Monthly Household Income:</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">₱{{ number_format($welfareCase->student->monthly_household_income ?? 0, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Case Assessment & Status Update Form -->
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200 dark:border-slate-800 shadow-xs">
                    <h3 class="text-xs font-extrabold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-4">
                        Case Assessment & Status
                    </h3>

                    <form method="POST" action="{{ route('admin.welfare-cases.update-status', $welfareCase->id) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Update Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" class="w-full py-2.5 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-[#3B060F] outline-none">
                                <option value="Open" {{ $welfareCase->status === 'Open' ? 'selected' : '' }}>Open</option>
                                <option value="Under Assessment" {{ $welfareCase->status === 'Under Assessment' ? 'selected' : '' }}>Under Assessment</option>
                                <option value="Referred" {{ $welfareCase->status === 'Referred' ? 'selected' : '' }}>Referred</option>
                                <option value="For Follow-up" {{ $welfareCase->status === 'For Follow-up' ? 'selected' : '' }}>For Follow-up</option>
                                <option value="Resolved" {{ $welfareCase->status === 'Resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="Closed" {{ $welfareCase->status === 'Closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                Staff Notes / Requested Information
                            </label>
                            <textarea name="requested_information" 
                                      rows="4" 
                                      placeholder="Enter administrative notes, instructions for student follow-up, or required additional info..." 
                                      class="w-full p-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-[#3B060F] outline-none">{{ old('requested_information', $welfareCase->requested_information) }}</textarea>
                            <p class="text-[10px] text-slate-400 mt-1">
                                The student will receive an automatic in-app and email alert whenever this is updated.
                            </p>
                        </div>

                        <button type="submit" class="w-full py-2.5 bg-[#3B060F] hover:bg-[#6B0F1A] text-white font-extrabold text-xs rounded-xl transition shadow-xs cursor-pointer">
                            Update Welfare Case
                        </button>
                    </form>
                </div>

            </div>

        </div>

        <!-- Document Inline Preview Modal -->
        <div x-show="previewModal" 
             x-cloak
             @keydown.escape.window="previewModal = false"
             class="fixed inset-0 z-50 overflow-hidden bg-slate-950/80 backdrop-blur-sm flex flex-col items-center justify-center p-2 sm:p-6">
            
            <div @click.away="previewModal = false" 
                 class="bg-white dark:bg-slate-900 rounded-2xl w-full max-w-5xl h-[90vh] flex flex-col shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                
                <!-- Modal Top Header -->
                <div class="px-5 py-3.5 bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between shrink-0">
                    <div class="flex items-center space-x-3 overflow-hidden">
                        <div class="h-8 w-8 rounded-lg bg-[#3B060F] text-[#FFC107] flex items-center justify-center shrink-0 font-bold text-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="overflow-hidden">
                            <h3 class="font-extrabold text-xs sm:text-sm text-slate-800 dark:text-slate-100 truncate" x-text="previewName">Document Preview</h3>
                            <p class="text-[10px] text-slate-400">OSDW Secure Document Viewer</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2">
                        <!-- Open in New Tab Button -->
                        <a :href="previewUrl" target="_blank" class="px-3 py-1.5 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold text-xs rounded-xl transition inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            <span class="hidden sm:inline">New Tab</span>
                        </a>

                        <!-- Close Button -->
                        <button type="button" @click="previewModal = false" class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-white rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Preview Content Box -->
                <div class="flex-1 bg-slate-100 dark:bg-slate-950 p-2 sm:p-4 overflow-auto flex items-center justify-center">
                    <!-- PDF Viewer -->
                    <template x-if="isPdf">
                        <iframe :src="previewUrl" class="w-full h-full rounded-xl border border-slate-200 dark:border-slate-800 bg-white" title="PDF Preview"></iframe>
                    </template>

                    <!-- Image Viewer -->
                    <template x-if="!isPdf">
                        <div class="max-h-full max-w-full overflow-auto flex items-center justify-center p-2">
                            <img :src="previewUrl" :alt="previewName" class="max-h-[80vh] max-w-full rounded-xl object-contain shadow-md border border-slate-200 dark:border-slate-800">
                        </div>
                    </template>
                </div>

            </div>
        </div>

    </div>

</x-app-layout>
