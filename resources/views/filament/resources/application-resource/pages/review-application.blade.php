<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- LEFT COLUMN (40%): Student Academic Snapshot & Rules Engine Audit -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- Student Bio Snapshot Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 shadow-xs">
                <div class="flex items-center space-x-4 border-b border-gray-100 dark:border-gray-700 pb-4 mb-4">
                    <div class="h-12 w-12 rounded-full bg-[#7B1113] text-[#D4AF37] font-extrabold flex items-center justify-center text-base border border-[#D4AF37]/40 shrink-0">
                        {{ strtoupper(substr($record->student->user->first_name ?? 'S', 0, 1) . substr($record->student->user->last_name ?? 'T', 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-gray-900 dark:text-white">
                            {{ $record->student->user->full_name ?? 'Student Name' }}
                        </h3>
                        <p class="text-xs text-gray-500 font-mono">{{ $record->student->student_number ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div>
                        <span class="text-gray-400 font-bold uppercase text-[10px] block">College</span>
                        <span class="font-extrabold text-gray-900 dark:text-white mt-0.5 block">
                            {{ $record->student->college?->value ?? $record->student->course ?? 'N/A' }}
                        </span>
                    </div>

                    <div>
                        <span class="text-gray-400 font-bold uppercase text-[10px] block">Year Level</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200 mt-0.5 block">
                            {{ $record->student->year_level ?? 'N/A' }}
                        </span>
                    </div>

                    <div class="col-span-2">
                        <span class="text-gray-400 font-bold uppercase text-[10px] block">Degree Program</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200 mt-0.5 block">
                            {{ $record->student->program ?? $record->student->course ?? 'N/A' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Rules Engine Audit Snapshot -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 shadow-xs space-y-4">
                <h4 class="text-xs font-extrabold text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 pb-2">
                    Campus Rules Engine Audit
                </h4>

                @php
                    $gwa = $record->student_gwa ?? $record->student?->current_gwa;
                    $minGwa = $record->scholarship->min_gwa;
                    $income = $record->monthly_income ?? $record->student?->monthly_household_income;
                    $maxIncome = $record->scholarship->max_household_income;

                    $gwaPassed = is_null($minGwa) || ($gwa && $gwa <= $minGwa);
                    $incomePassed = is_null($maxIncome) || ($income && $income <= $maxIncome);
                @endphp

                <!-- GWA Audit indicator -->
                <div class="p-3 rounded-lg border flex items-center justify-between {{ $gwaPassed ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-rose-50 border-rose-200 text-rose-900' }}">
                    <div>
                        <div class="text-xs font-extrabold">Student GWA: {{ $gwa ? number_format($gwa, 2) : 'N/A' }}</div>
                        <div class="text-[11px] opacity-80">Program Cutoff: {{ $minGwa ? '≤ ' . number_format($minGwa, 2) : 'No Cutoff' }}</div>
                    </div>
                    @if($gwaPassed)
                        <span class="h-7 w-7 rounded-full bg-emerald-500 text-white font-bold flex items-center justify-center text-xs">✓</span>
                    @else
                        <span class="h-7 w-7 rounded-full bg-rose-600 text-white font-bold flex items-center justify-center text-xs">✕</span>
                    @endif
                </div>

                <!-- Household Income Audit indicator -->
                <div class="p-3 rounded-lg border flex items-center justify-between {{ $incomePassed ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-rose-50 border-rose-200 text-rose-900' }}">
                    <div>
                        <div class="text-xs font-extrabold">Monthly Income: ₱{{ number_format($income ?? 0, 2) }}</div>
                        <div class="text-[11px] opacity-80">Ceiling: {{ $maxIncome ? '≤ ₱' . number_format($maxIncome, 2) : 'No Ceiling' }}</div>
                    </div>
                    @if($incomePassed)
                        <span class="h-7 w-7 rounded-full bg-emerald-500 text-white font-bold flex items-center justify-center text-xs">✓</span>
                    @else
                        <span class="h-7 w-7 rounded-full bg-rose-600 text-white font-bold flex items-center justify-center text-xs">✕</span>
                    @endif
                </div>

                <!-- 4Ps Beneficiary Status -->
                <div class="flex items-center justify-between text-xs pt-1">
                    <span class="font-bold text-gray-600 dark:text-gray-400">4Ps Beneficiary:</span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $record->student?->is_4ps ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                        {{ $record->student?->is_4ps ? 'Yes (4Ps Grantee)' : 'No' }}
                    </span>
                </div>
            </div>

            <!-- Attached Requirements List -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 shadow-xs space-y-3">
                <h4 class="text-xs font-extrabold text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 pb-2">
                    Submitted Requirement Documents ({{ $record->documents->count() }})
                </h4>

                <div class="space-y-2">
                    @forelse($record->documents as $doc)
                        <div @click="$wire.selectDoc({{ $doc->id }})"
                             class="p-3 rounded-lg border cursor-pointer transition flex items-center justify-between text-xs
                             {{ $selectedDoc && $selectedDoc->id === $doc->id ? 'bg-[#7B1113]/10 border-[#7B1113] text-[#7B1113] font-bold' : 'bg-gray-50 dark:bg-gray-700/50 border-gray-200 dark:border-gray-600' }}">
                            <div>
                                <div>{{ $doc->doc_type ?? $doc->requirement->requirement_name ?? 'Document' }}</div>
                                <div class="text-[10px] text-gray-400 font-mono">{{ $doc->original_filename }}</div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase
                                @if($doc->status === 'verified') bg-emerald-100 text-emerald-800
                                @elseif($doc->status === 'needs_resubmission') bg-rose-100 text-rose-800
                                @else bg-amber-100 text-amber-800 @endif">
                                {{ str_replace('_', ' ', $doc->status) }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-4 text-xs text-gray-400">No documents attached.</div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN (60%): Interactive Document Inspector Viewport -->
        <div class="lg:col-span-7 flex flex-col space-y-4">
            
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 shadow-xs flex-1 flex flex-col">
                @if($selectedDoc)
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-3 mb-3 shrink-0">
                        <div>
                            <h3 class="text-sm font-extrabold text-gray-900 dark:text-white">
                                {{ $selectedDoc->doc_type ?? $selectedDoc->requirement->requirement_name ?? 'Document Preview' }}
                            </h3>
                            <p class="text-[11px] text-gray-400 font-mono">{{ $selectedDoc->original_filename }}</p>
                        </div>

                        <div class="flex items-center space-x-2">
                            <!-- Verify Button -->
                            @if($selectedDoc->status !== 'verified')
                                <x-filament::button size="sm" color="success" wire:click="verifyDoc({{ $selectedDoc->id }})">
                                    Verify Document
                                </x-filament::button>
                            @endif
                        </div>
                    </div>

                    <!-- In-App Document Viewer Viewport -->
                    @php
                        $fileUrl = route('admin.applications.documents.download', $selectedDoc->id);
                        $isImage = \Illuminate\Support\Str::endsWith(strtolower($selectedDoc->original_filename), ['.jpg', '.jpeg', '.png']);
                    @endphp

                    <div class="flex-1 bg-gray-900 rounded-lg min-h-[450px] flex items-center justify-center overflow-hidden relative">
                        @if($isImage)
                            <img src="{{ $fileUrl }}" alt="Document Image" class="max-h-[500px] max-w-full object-contain p-2">
                        @else
                            <iframe src="{{ $fileUrl }}#toolbar=0" class="w-full h-full min-h-[500px] border-0"></iframe>
                        @endif
                    </div>

                    <!-- Deficient Remarks Input Section -->
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row gap-3 items-end">
                        <div class="flex-1 w-full">
                            <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-400 mb-1">Evaluator Deficiency Remarks (if rejecting copy)</label>
                            <input type="text" wire:model="rejectionRemarks" placeholder="e.g., Missing official seal, unreadable grade entry..." class="w-full text-xs rounded-lg border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 py-2 px-3">
                        </div>

                        <x-filament::button size="sm" color="danger" wire:click="rejectDoc({{ $selectedDoc->id }})">
                            Flag Deficient / Reject Copy
                        </x-filament::button>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center min-h-[400px] text-gray-400 text-xs">
                        Select a document from the left column to inspect.
                    </div>
                @endif
            </div>

            <!-- Page Action Control Footer -->
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 shadow-md flex items-center justify-between gap-4">
                <x-filament::button color="warning" wire:click="markDeficient">
                    Return to Student for Resubmission
                </x-filament::button>

                <x-filament::button color="success" size="lg" wire:click="endorseQualified">
                    Endorse as Qualified & Approve
                </x-filament::button>
            </div>

        </div>

    </div>
</x-filament-panels::page>
