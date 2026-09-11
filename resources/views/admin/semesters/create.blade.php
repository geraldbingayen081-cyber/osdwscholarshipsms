<x-app-layout title="Create Semester - CSU–Lal-lo">
    <x-slot name="header">
        Create Semester
    </x-slot>

    <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-xs border border-slate-200 p-6 sm:p-8">
        <div class="mb-6 border-b border-slate-200 pb-4">
            <h3 class="text-base font-bold text-slate-800">New Semester Registration</h3>
            <p class="text-xs text-slate-500 mt-1">Configure term dates under an Academic Year.</p>
        </div>

        <form method="POST" action="{{ route('admin.semesters.store') }}" class="space-y-6">
            @csrf

            <!-- Academic Year -->
            <div>
                <label for="academic_year_id" class="block text-xs font-semibold text-slate-700 mb-1">
                    Academic Year <span class="text-red-500">*</span>
                </label>
                <select 
                    name="academic_year_id" 
                    id="academic_year_id" 
                    required 
                    class="w-full px-3 py-2 rounded-lg border @error('academic_year_id') border-red-500 bg-red-50 @else border-slate-300 @enderror text-sm focus:ring-2 focus:ring-csu-green focus:border-csu-green"
                >
                    <option value="">-- Select Academic Year --</option>
                    @foreach($academicYears as $ay)
                        <option value="{{ $ay->id }}" {{ old('academic_year_id') == $ay->id ? 'selected' : '' }}>
                            {{ $ay->name }} {{ $ay->status === 'active' ? '(Active Year)' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('academic_year_id')
                    <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Semester Name -->
            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">
                    Semester Name <span class="text-red-500">*</span>
                </label>
                <select 
                    name="name" 
                    id="name" 
                    required 
                    class="w-full px-3 py-2 rounded-lg border @error('name') border-red-500 bg-red-50 @else border-slate-300 @enderror text-sm focus:ring-2 focus:ring-csu-green focus:border-csu-green"
                >
                    <option value="">-- Select Predefined Term --</option>
                    @foreach($semesterNames as $semName)
                        <option value="{{ $semName }}" {{ old('name') === $semName ? 'selected' : '' }}>
                            {{ $semName }}
                        </option>
                    @endforeach
                </select>
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
                    Status <span class="text-red-500">*</span>
                </label>
                <select 
                    name="status" 
                    id="status" 
                    required 
                    class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-csu-green focus:border-csu-green"
                >
                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-200">
                <a href="{{ route('admin.semesters.index') }}" class="px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-800">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 bg-csu-green text-white font-bold rounded-lg text-xs hover:bg-csu-green-dark transition shadow-sm">
                    Save Semester
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
