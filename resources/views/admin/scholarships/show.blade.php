<x-app-layout title="{{ $scholarship->name }} - CSU–Lal-lo">
    <x-slot name="header">
        Scholarship Details & Requirements
    </x-slot>

    <!-- Header Actions & Overview -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.scholarships.index') }}" class="text-xs font-bold text-csu-green hover:underline inline-flex items-center gap-1 mb-1">
                &larr; Back to Scholarships List
            </a>
            <h2 class="text-xl font-bold text-slate-800">{{ $scholarship->name }}</h2>
            <p class="text-xs text-slate-500">Provider: <span class="font-semibold text-slate-700">{{ $scholarship->provider }}</span> | School Year: <span class="font-semibold text-slate-700">{{ $scholarship->school_year_label }}</span></p>
        </div>
        <div class="flex items-center space-x-2 shrink-0">
            <!-- Quick Status Change Form -->
            <form method="POST" action="{{ route('admin.scholarships.update-status', $scholarship->id) }}" class="inline-block">
                @csrf
                @method('PATCH')
                <select name="status" onchange="this.form.submit()" class="px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-bold text-slate-800 focus:ring-csu-green focus:border-csu-green">
                    <option value="draft" {{ $scholarship->status === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="open" {{ $scholarship->status === 'open' ? 'selected' : '' }}>Open</option>
                    <option value="closed" {{ $scholarship->status === 'closed' ? 'selected' : '' }}>Closed</option>
                    <option value="archived" {{ $scholarship->status === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </form>

            <a href="{{ route('admin.scholarships.edit', $scholarship->id) }}" class="px-3.5 py-1.5 bg-slate-800 text-white rounded-lg text-xs font-bold hover:bg-slate-900 transition">
                Edit Details
            </a>
        </div>
    </div>

    <!-- Scholarship Info Card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Details Column -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-xs border border-slate-200 p-6 space-y-4">
            <div>
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Description</h4>
                <p class="text-sm text-slate-700 mt-1 leading-relaxed">{{ $scholarship->description }}</p>
            </div>

            <div class="border-t border-slate-100 pt-3">
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Benefits & Financial Assistance</h4>
                <p class="text-sm text-csu-green font-semibold mt-1">{{ $scholarship->benefits }}</p>
            </div>

            <div class="border-t border-slate-100 pt-3 grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
                <div>
                    <span class="text-slate-400 font-semibold block">Coverage Type</span>
                    <span class="font-bold text-[#3B060F] inline-flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full {{ $scholarship->isContinuing() ? 'bg-emerald-500' : 'bg-blue-500' }}"></span>
                        {{ $scholarship->coverage_type_label }}
                    </span>
                </div>
                <div>
                    <span class="text-slate-400 font-semibold block">School Year</span>
                    <span class="font-bold text-slate-800">{{ $scholarship->school_year_label }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-semibold block">Start Date</span>
                    <span class="font-bold text-slate-800">{{ $scholarship->application_start_date->format('M d, Y') }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-semibold block">Application Deadline</span>
                    <span class="font-bold text-slate-800">{{ $scholarship->application_deadline->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Stats & Slot Tracker -->
        <div class="bg-white rounded-xl shadow-xs border border-slate-200 p-6 flex flex-col justify-between space-y-4">
            <div>
                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Slot Allocation Tracker</h4>
                <div class="mt-4 flex items-baseline justify-between">
                    <span class="text-3xl font-extrabold text-slate-800">{{ $scholarship->remaining_slots }}/{{ $scholarship->available_slots }}</span>
                    <span class="text-xs font-semibold text-slate-500">Available Slots</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-3 mt-2 overflow-hidden border border-slate-200">
                    @php
                        $percentage = min(100, round(($scholarship->approved_scholars_count / max(1, $scholarship->available_slots)) * 100));
                    @endphp
                    <div class="bg-csu-green h-3 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                </div>
                <p class="text-[11px] text-slate-500 mt-1.5">{{ $scholarship->remaining_slots }} slots remaining ({{ $scholarship->approved_scholars_count }} approved / {{ $scholarship->available_slots }} total)</p>
            </div>

            <div class="border-t border-slate-100 pt-4">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-500">Submitted Applications:</span>
                    <span class="font-bold text-slate-800">{{ $scholarship->applications_count }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Requirements Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Requirements List (2 cols) -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Eligibility Requirements -->
            <div class="bg-white rounded-xl shadow-xs border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-csu-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Eligibility Requirements
                    </h3>
                </div>

                <ul class="divide-y divide-slate-200">
                    @forelse($scholarship->requirements->where('requirement_type', 'eligibility') as $req)
                        <li class="p-4 flex items-center justify-between hover:bg-slate-50">
                            <div class="flex items-center space-x-3">
                                <span class="h-2 w-2 rounded-full bg-csu-green"></span>
                                <span class="text-sm text-slate-700 font-medium">{{ $req->requirement_name }}</span>
                            </div>
                            <form method="POST" action="{{ route('admin.scholarship-requirements.destroy', $req->id) }}" onsubmit="return confirm('Remove this eligibility requirement?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-semibold">
                                    Remove
                                </button>
                            </form>
                        </li>
                    @empty
                        <li class="p-6 text-center text-xs text-slate-400">No eligibility rules added yet.</li>
                    @endforelse
                </ul>
            </div>

            <!-- Document Requirements -->
            <div class="bg-white rounded-xl shadow-xs border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-csu-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Required Submission Documents
                    </h3>
                </div>

                <ul class="divide-y divide-slate-200">
                    @forelse($scholarship->requirements->where('requirement_type', 'document') as $req)
                        <li class="p-4 flex items-center justify-between hover:bg-slate-50">
                            <div class="flex items-center space-x-3">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="text-sm text-slate-700 font-medium">{{ $req->requirement_name }}</span>
                                @if($req->is_required)
                                    <span class="text-[10px] bg-red-100 text-red-700 font-bold px-1.5 py-0.5 rounded">Mandatory</span>
                                @endif
                            </div>
                            <form method="POST" action="{{ route('admin.scholarship-requirements.destroy', $req->id) }}" onsubmit="return confirm('Remove this document requirement?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-semibold">
                                    Remove
                                </button>
                            </form>
                        </li>
                    @empty
                        <li class="p-6 text-center text-xs text-slate-400">No document upload requirements added yet.</li>
                    @endforelse
                </ul>
            </div>

        </div>

        <!-- Add Requirement Form (1 col) -->
        <div>
            <div 
                x-data="{ reqType: 'eligibility', selectedPreset: '', isCustom: false }" 
                class="bg-white rounded-xl shadow-xs border border-slate-200 p-6 sticky top-6"
            >
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-4 border-b border-slate-200 pb-3">
                    Add Program Requirement
                </h3>

                <form method="POST" action="{{ route('admin.scholarship-requirements.store', $scholarship->id) }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="requirement_type" class="block text-xs font-semibold text-slate-700 mb-1">
                            Requirement Type <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="requirement_type" 
                            id="requirement_type" 
                            x-model="reqType"
                            @change="selectedPreset = ''; isCustom = false"
                            required 
                            class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs focus:ring-csu-green focus:border-csu-green"
                        >
                            <option value="eligibility">Eligibility Criterion</option>
                            <option value="document">Required Document Upload</option>
                        </select>
                    </div>

                    <!-- Preset Selection Dropdown for Eligibility -->
                    <template x-if="reqType === 'eligibility'">
                        <div>
                            <label for="eligibility_preset" class="block text-xs font-semibold text-slate-700 mb-1">
                                Select Eligibility Preset <span class="text-red-500">*</span>
                            </label>
                            <select 
                                name="preset_name" 
                                id="eligibility_preset" 
                                x-model="selectedPreset"
                                @change="isCustom = (selectedPreset === 'custom')"
                                class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs focus:ring-csu-green focus:border-csu-green"
                            >
                                <option value="">-- Choose Eligibility Preset --</option>
                                <option value="Minimum GWA Cutoff Requirement (1.75 or better)">Minimum GWA Cutoff Requirement (1.75 or better)</option>
                                <option value="Maximum Monthly Household Income Ceiling">Maximum Monthly Household Income Ceiling</option>
                                <option value="Bona fide Resident of Lal-lo / Cagayan Province">Bona fide Resident of Lal-lo / Cagayan Province</option>
                                <option value="Full-Time Student Enrolled in CSU Lal-lo">Full-Time Student Enrolled in CSU Lal-lo</option>
                                <option value="Single Grant Policy (No Conflicting Government Scholarship)">Single Grant Policy (No Conflicting Government Scholarship)</option>
                                <option value="Good Moral Character / No Disciplinary Record">Good Moral Character / No Disciplinary Record</option>
                                <option value="custom">+ Custom Eligibility Criterion</option>
                            </select>
                        </div>
                    </template>

                    <!-- Preset Selection Dropdown for Document Upload -->
                    <template x-if="reqType === 'document'">
                        <div>
                            <label for="document_preset" class="block text-xs font-semibold text-slate-700 mb-1">
                                Select Document Slot Preset <span class="text-red-500">*</span>
                            </label>
                            <select 
                                name="preset_name" 
                                id="document_preset" 
                                x-model="selectedPreset"
                                @change="isCustom = (selectedPreset === 'custom')"
                                class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs focus:ring-csu-green focus:border-csu-green"
                            >
                                <option value="">-- Choose Document Preset --</option>
                                <option value="Certificate of Enrollment (COE)">Certificate of Enrollment (COE)</option>
                                <option value="Certificate of Registration (COR)">Certificate of Registration (COR)</option>
                                <option value="Certificate of Grades (COG)">Certificate of Grades (COG)</option>
                                <option value="Certificate of Indigency / Income Tax Return (ITR)">Certificate of Indigency / Income Tax Return (ITR)</option>
                                <option value="Valid Student ID Card">Valid Student ID Card</option>
                                <option value="Certificate of Good Moral Character">Certificate of Good Moral Character</option>
                                <option value="Solo Parent ID / PWD ID (if applicable)">Solo Parent ID / PWD ID (if applicable)</option>
                                <option value="Barangay Clearance">Barangay Clearance</option>
                                <option value="custom">+ Custom Document Upload Slot</option>
                            </select>
                        </div>
                    </template>

                    <!-- Custom Text Entry (Shown if 'custom' option selected) -->
                    <div x-show="isCustom" x-transition>
                        <label for="custom_name" class="block text-xs font-semibold text-slate-700 mb-1">
                            Custom Requirement Title/Description <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="custom_name" 
                            id="custom_name" 
                            class="w-full px-3 py-2 rounded-lg border border-slate-300 text-xs focus:ring-csu-green focus:border-csu-green"
                            placeholder="Enter custom requirement name..."
                        >
                    </div>

                    <div class="flex items-center space-x-2 pt-1">
                        <input type="checkbox" name="is_required" id="is_required" value="1" checked class="rounded border-slate-300 text-csu-green focus:ring-csu-green h-4 w-4">
                        <label for="is_required" class="text-xs text-slate-600 font-medium">Mandatory Requirement</label>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-csu-green text-white font-bold rounded-lg text-xs hover:bg-csu-green-dark transition shadow-sm">
                        + Add Requirement
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
