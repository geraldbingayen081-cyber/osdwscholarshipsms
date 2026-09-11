<x-app-layout title="Registered Students - CSU Lal-lo Admin">

    <!-- Header Section -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Registered Students Roster</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">
                View and manage student profiles registered on the CSU Lal-lo OSDW Scholarship Portal.
            </p>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 p-4 mb-6">
        <form method="GET" action="{{ route('admin.students.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <!-- Search Input -->
            <div class="sm:col-span-7 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" 
                       placeholder="Search student name, email, or Student ID (e.g. 26-32424)..." 
                       {{ $search ? 'autofocus onfocus="this.setSelectionRange(this.value.length, this.value.length)"' : '' }}
                       oninput="clearTimeout(window._searchTimer); window._searchTimer = setTimeout(() => this.form.submit(), 400)"
                       class="w-full py-2 pl-9 pr-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
            </div>

            <!-- Course Select Filter -->
            <div class="sm:col-span-5 flex items-center gap-2">
                <select name="course" onchange="this.form.submit()" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 font-semibold focus:ring-2 focus:ring-[#6B0F1A] focus:outline-none">
                    <option value="">All Degree Courses</option>
                    @foreach($courses as $c)
                        <option value="{{ $c }}" {{ $course === $c ? 'selected' : '' }}>
                            {{ $c }}
                        </option>
                    @endforeach
                </select>
                @if($search || $course)
                    <a href="{{ route('admin.students.index') }}" class="px-2.5 py-1.5 text-xs font-bold text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition shrink-0" title="Reset Filters">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Students Data Table -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5">Student Name</th>
                        <th class="px-6 py-3.5">Student ID</th>
                        <th class="px-6 py-3.5">Course & Year</th>
                        <th class="px-6 py-3.5">Applications</th>
                        <th class="px-6 py-3.5">Scholar Standing</th>
                        <th class="px-6 py-3.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($students as $student)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    @if($student->user->profile_photo_url)
                                        <img src="{{ $student->user->profile_photo_url }}" alt="{{ $student->user->full_name }}" class="h-9 w-9 rounded-full object-cover border border-[#6B0F1A] shrink-0">
                                    @else
                                        <div class="h-9 w-9 rounded-full bg-[#3B060F] text-[#FFC107] font-bold flex items-center justify-center text-xs shrink-0">
                                            {{ strtoupper(substr($student->user->first_name, 0, 1) . substr($student->user->last_name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-bold text-slate-900 text-sm">
                                            {{ $student->user->full_name }}
                                        </div>
                                        <div class="text-[11px] text-slate-500">
                                            {{ $student->user->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 font-mono font-bold text-slate-800">
                                {{ $student->student_number }}
                            </td>

                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-800">
                                    {{ $student->course }}
                                </div>
                                <div class="text-[11px] text-slate-400">
                                    {{ $student->year_level }}
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $student->applications->count() }} Submitted
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                @if($student->scholars->isNotEmpty())
                                    @php $activeScholar = $student->scholars->first(); @endphp
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        Scholar: {{ $activeScholar->scholarship->name ?? 'Active' }}
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs font-medium">None</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.students.show', $student->id) }}" 
                                   class="inline-flex items-center px-3.5 py-1.5 bg-[#3B060F] text-white font-bold rounded-lg text-xs hover:bg-[#6B0F1A] transition shadow-xs">
                                    View Profile
                                    <svg class="w-3.5 h-3.5 ml-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500 text-xs">
                                No registered students found matching search filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $students->links() }}
            </div>
        @endif
    </div>

</x-app-layout>
