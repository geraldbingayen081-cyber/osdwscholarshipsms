<x-app-layout title="Semesters - CSU–Lal-lo">
    <x-slot name="header">
        Semester Management
    </x-slot>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-slate-800">Academic Semesters</h3>
            <p class="text-xs text-slate-500">Manage terms under each Academic Year.</p>
        </div>
        <a href="{{ route('admin.semesters.create') }}" class="px-4 py-2 bg-csu-green text-white font-bold rounded-lg text-xs hover:bg-csu-green-dark transition shadow-sm inline-flex items-center gap-1.5 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Create Semester
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="mb-6 bg-white rounded-xl shadow-xs border border-slate-200 p-4">
        <form method="GET" action="{{ route('admin.semesters.index') }}" class="flex items-center gap-4">
            <div class="w-full sm:w-64">
                <label for="academic_year_id" class="block text-xs font-semibold text-slate-700 mb-1">Filter by Academic Year</label>
                <select name="academic_year_id" id="academic_year_id" onchange="this.form.submit()" class="w-full px-3 py-1.5 rounded-lg border border-slate-300 text-xs focus:ring-csu-green focus:border-csu-green">
                    <option value="">-- All Academic Years --</option>
                    @foreach($academicYears as $ay)
                        <option value="{{ $ay->id }}" {{ $academicYearId == $ay->id ? 'selected' : '' }}>
                            {{ $ay->name }} {{ $ay->status === 'active' ? '(Active)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            @if($academicYearId)
                <div class="pt-5">
                    <a href="{{ route('admin.semesters.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">Clear Filter</a>
                </div>
            @endif
        </form>
    </div>

    <!-- Semesters Table -->
    <div class="bg-white rounded-xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs font-bold text-slate-500 uppercase border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5">Academic Year</th>
                        <th class="px-6 py-3.5">Semester Name</th>
                        <th class="px-6 py-3.5">Start Date</th>
                        <th class="px-6 py-3.5">End Date</th>
                        <th class="px-6 py-3.5">Scholarships</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($semesters as $sem)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-bold text-slate-800">
                                {{ $sem->academicYear->name }}
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-slate-800">
                                {{ $sem->name }}
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600">
                                {{ $sem->start_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600">
                                {{ $sem->end_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-slate-700">
                                {{ $sem->scholarships_count }} Programs
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold capitalize {{ $sem->status === 'active' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-slate-100 text-slate-600 border border-slate-300' }}">
                                    {{ $sem->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.semesters.edit', $sem->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-extrabold bg-slate-800 text-white rounded-xl hover:bg-slate-900 transition shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-slate-500">
                                No semesters recorded for the selected filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
            {{ $semesters->links() }}
        </div>
    </div>
</x-app-layout>
