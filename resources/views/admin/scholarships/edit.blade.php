<x-app-layout title="Edit Scholarship - CSU–Lal-lo">
    <x-slot name="header">
        Edit Scholarship Program
    </x-slot>

    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-xs border border-slate-200 p-6 sm:p-8">
        <div class="mb-6 border-b border-slate-200 pb-4">
            <h3 class="text-base font-bold text-slate-800">Edit Program: {{ $scholarship->name }}</h3>
            <p class="text-xs text-slate-500 mt-1">Update details, slots, or deadlines for this program.</p>
        </div>

        <form method="POST" action="{{ route('admin.scholarships.update', $scholarship->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Program Name & Provider -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">
                        Scholarship Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        value="{{ old('name', $scholarship->name) }}" 
                        required 
                        class="w-full px-3 py-2 rounded-lg border @error('name') border-red-500 bg-red-50 @else border-slate-300 @enderror text-sm focus:ring-2 focus:ring-csu-green focus:border-csu-green"
                    >
                    @error('name')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="provider" class="block text-xs font-semibold text-slate-700 mb-1">
                        Scholarship Provider / Donor <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="provider" 
                        id="provider" 
                        value="{{ old('provider', $scholarship->provider) }}" 
                        required 
                        class="w-full px-3 py-2 rounded-lg border @error('provider') border-red-500 bg-red-50 @else border-slate-300 @enderror text-sm focus:ring-2 focus:ring-csu-green focus:border-csu-green"
                    >
                    @error('provider')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-xs font-semibold text-slate-700 mb-1">
                    Program Description <span class="text-red-500">*</span>
                </label>
                <textarea 
                    name="description" 
                    id="description" 
                    rows="3" 
                    required 
                    class="w-full px-3 py-2 rounded-lg border @error('description') border-red-500 bg-red-50 @else border-slate-300 @enderror text-sm focus:ring-2 focus:ring-csu-green focus:border-csu-green"
                >{{ old('description', $scholarship->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Benefits -->
            <div>
                <label for="benefits" class="block text-xs font-semibold text-slate-700 mb-1">
                    Coverage & Benefits <span class="text-red-500">*</span>
                </label>
                <textarea 
                    name="benefits" 
                    id="benefits" 
                    rows="2" 
                    required 
                    class="w-full px-3 py-2 rounded-lg border @error('benefits') border-red-500 bg-red-50 @else border-slate-300 @enderror text-sm focus:ring-2 focus:ring-csu-green focus:border-csu-green"
                >{{ old('benefits', $scholarship->benefits) }}</textarea>
                @error('benefits')
                    <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- School Year & Campus Rules Engine -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="school_year" class="block text-xs font-semibold text-slate-700 mb-1">
                        School Year <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="school_year" 
                        id="school_year" 
                        value="{{ old('school_year', $scholarship->school_year_label) }}" 
                        required 
                        pattern="\d{4}-\d{4}"
                        placeholder="e.g. 2026-2027"
                        class="w-full px-3 py-2 rounded-lg border @error('school_year') border-red-500 bg-red-50 @else border-slate-300 @enderror text-sm focus:ring-2 focus:ring-csu-green focus:border-csu-green"
                    >
                    <p class="text-[10px] text-slate-400 mt-1">Must be consecutive years e.g. 2026-2027</p>
                    @error('school_year')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="min_gwa" class="block text-xs font-semibold text-slate-700 mb-1">
                        Minimum GWA Cutoff <span class="text-xs text-slate-400 font-normal">(Optional)</span>
                    </label>
                    <input 
                        type="number" 
                        step="0.01" 
                        min="1.00" 
                        max="5.00"
                        name="min_gwa" 
                        id="min_gwa" 
                        value="{{ old('min_gwa', $scholarship->min_gwa) }}" 
                        placeholder="e.g. 1.75"
                        class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-csu-green focus:border-csu-green"
                    >
                </div>

                <div>
                    <label for="max_household_income" class="block text-xs font-semibold text-slate-700 mb-1">
                        Max Income Ceiling (₱) <span class="text-xs text-slate-400 font-normal">(Optional)</span>
                    </label>
                    <input 
                        type="number" 
                        step="500" 
                        min="0" 
                        name="max_household_income" 
                        id="max_household_income" 
                        value="{{ old('max_household_income', $scholarship->max_household_income) }}" 
                        placeholder="e.g. 25000"
                        class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-csu-green focus:border-csu-green"
                    >
                </div>
            </div>

            <!-- Slots & Application Dates -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="available_slots" class="block text-xs font-semibold text-slate-700 mb-1">
                        Available Slots <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        name="available_slots" 
                        id="available_slots" 
                        value="{{ old('available_slots', $scholarship->available_slots) }}" 
                        min="1" 
                        required 
                        class="w-full px-3 py-2 rounded-lg border @error('available_slots') border-red-500 bg-red-50 @else border-slate-300 @enderror text-sm focus:ring-2 focus:ring-csu-green focus:border-csu-green"
                    >
                    @error('available_slots')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="application_start_date" class="block text-xs font-semibold text-slate-700 mb-1">
                        Start Date <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        name="application_start_date" 
                        id="application_start_date" 
                        value="{{ old('application_start_date', $scholarship->application_start_date->format('Y-m-d')) }}" 
                        required 
                        class="w-full px-3 py-2 rounded-lg border @error('application_start_date') border-red-500 bg-red-50 @else border-slate-300 @enderror text-sm focus:ring-2 focus:ring-csu-green focus:border-csu-green"
                    >
                    @error('application_start_date')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="application_deadline" class="block text-xs font-semibold text-slate-700 mb-1">
                        Deadline <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        name="application_deadline" 
                        id="application_deadline" 
                        value="{{ old('application_deadline', $scholarship->application_deadline->format('Y-m-d')) }}" 
                        required 
                        class="w-full px-3 py-2 rounded-lg border @error('application_deadline') border-red-500 bg-red-50 @else border-slate-300 @enderror text-sm focus:ring-2 focus:ring-csu-green focus:border-csu-green"
                    >
                    @error('application_deadline')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Coverage Type & Status -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="coverage_type" class="block text-xs font-semibold text-slate-700 mb-1">
                        Scholarship Coverage Type <span class="text-red-500">*</span>
                    </label>
                    <select name="coverage_type" id="coverage_type" required class="w-full px-3 py-2 rounded-lg border @error('coverage_type') border-red-500 bg-red-50 @else border-slate-300 @enderror text-sm focus:ring-csu-green focus:border-csu-green">
                        <option value="continuing" {{ old('coverage_type', $scholarship->coverage_type) === 'continuing' ? 'selected' : '' }}>
                            Continuing / Multi-Year (e.g. 1st–4th Year, periodic compliance per semester)
                        </option>
                        <option value="annual" {{ old('coverage_type', $scholarship->coverage_type) === 'annual' ? 'selected' : '' }}>
                            School Year-Based / Annual (Valid for 1 School Year)
                        </option>
                    </select>
                    <p class="text-[10px] text-slate-400 mt-1">Determines how long scholar records remain active without a new application.</p>
                    @error('coverage_type')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-xs font-semibold text-slate-700 mb-1">
                        Program Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status" required class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-csu-green focus:border-csu-green">
                        <option value="draft" {{ old('status', $scholarship->status) === 'draft' ? 'selected' : '' }}>Draft (Private)</option>
                        <option value="open" {{ old('status', $scholarship->status) === 'open' ? 'selected' : '' }}>Open (Accepting Student Applications)</option>
                        <option value="closed" {{ old('status', $scholarship->status) === 'closed' ? 'selected' : '' }}>Closed</option>
                        <option value="archived" {{ old('status', $scholarship->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-200">
                <a href="{{ route('admin.scholarships.show', $scholarship->id) }}" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-800">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 bg-csu-green text-white font-bold rounded-lg text-xs hover:bg-csu-green-dark transition shadow-sm">
                    Update Scholarship Details
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
