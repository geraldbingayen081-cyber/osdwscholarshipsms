<x-app-layout title="Create Academic Year - CSU–Lal-lo">
    <x-slot name="header">
        Create Academic Year
    </x-slot>

    <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-xs border border-slate-200 p-6 sm:p-8">
        <div class="mb-6 border-b border-slate-200 pb-4">
            <h3 class="text-base font-bold text-slate-800">New Academic Year Configuration</h3>
            <p class="text-xs text-slate-500 mt-1">Set up a new academic year session for CSU–Lal-lo scholarship programs.</p>
        </div>

        <form method="POST" action="{{ route('admin.academic-years.store') }}" class="space-y-6">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">
                    Academic Year Name <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    value="{{ old('name') }}" 
                    required 
                    class="w-full px-3 py-2 rounded-lg border @error('name') border-red-500 bg-red-50 @else border-slate-300 @enderror text-sm focus:ring-2 focus:ring-csu-green focus:border-csu-green"
                    placeholder="e.g. AY 2026-2027"
                >
                @error('name')
                    <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Dates Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Start Date -->
                <div>
                    <label for="start_date" class="block text-xs font-semibold text-slate-700 mb-1">
                        Start Date <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        name="start_date" 
                        id="start_date" 
                        value="{{ old('start_date') }}" 
                        required 
                        class="w-full px-3 py-2 rounded-lg border @error('start_date') border-red-500 bg-red-50 @else border-slate-300 @enderror text-sm focus:ring-2 focus:ring-csu-green focus:border-csu-green"
                    >
                    @error('start_date')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- End Date -->
                <div>
                    <label for="end_date" class="block text-xs font-semibold text-slate-700 mb-1">
                        End Date <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        name="end_date" 
                        id="end_date" 
                        value="{{ old('end_date') }}" 
                        required 
                        class="w-full px-3 py-2 rounded-lg border @error('end_date') border-red-500 bg-red-50 @else border-slate-300 @enderror text-sm focus:ring-2 focus:ring-csu-green focus:border-csu-green"
                    >
                    @error('end_date')
                        <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-xs font-semibold text-slate-700 mb-1">
                    Initial Status <span class="text-red-500">*</span>
                </label>
                <select 
                    name="status" 
                    id="status" 
                    required 
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-csu-green focus:border-csu-green"
                >
                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active (Will deactivate previous active AY)</option>
                </select>
                <p class="mt-1 text-[11px] text-slate-500">Selecting 'Active' will automatically set any existing active Academic Year to 'Inactive'.</p>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-200">
                <a href="{{ route('admin.academic-years.index') }}" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-800">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 bg-csu-green text-white font-bold rounded-lg text-xs hover:bg-csu-green-dark transition shadow-sm">
                    Save Academic Year
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
