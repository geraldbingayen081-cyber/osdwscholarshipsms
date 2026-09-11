<x-app-layout title="Academic Years - CSU–Lal-lo">
    <x-slot name="header">
        Academic Year Management
    </x-slot>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-slate-800">Academic Years & Sessions</h3>
            <p class="text-xs text-slate-500">Only ONE Academic Year can be active at a time. Activating a new year automatically deactivates the current active year.</p>
        </div>
        <a href="{{ route('admin.academic-years.create') }}" class="px-4 py-2 bg-csu-green text-white font-bold rounded-lg text-xs hover:bg-csu-green-dark transition shadow-sm inline-flex items-center gap-1.5 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Create Academic Year
        </a>
    </div>

    <!-- Academic Years Table -->
    <div class="bg-white rounded-xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs font-bold text-slate-500 uppercase border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5">Academic Year Name</th>
                        <th class="px-6 py-3.5">Start Date</th>
                        <th class="px-6 py-3.5">End Date</th>
                        <th class="px-6 py-3.5">Semesters</th>
                        <th class="px-6 py-3.5">Scholarships</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($academicYears as $ay)
                        <tr class="hover:bg-slate-50 {{ $ay->status === 'active' ? 'bg-emerald-50/40' : '' }}">
                            <td class="px-6 py-4 font-bold text-slate-800 flex items-center gap-2">
                                {{ $ay->name }}
                                @if($ay->status === 'active')
                                    <span class="px-2 py-0.5 text-[10px] font-extrabold uppercase rounded bg-csu-green text-white">Active</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600">
                                {{ $ay->start_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600">
                                {{ $ay->end_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-slate-700">
                                {{ $ay->semesters_count }} Semesters
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-slate-700">
                                {{ $ay->scholarships_count }} Programs
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold capitalize {{ $ay->status === 'active' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-slate-100 text-slate-600 border border-slate-300' }}">
                                    {{ $ay->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-1">
                                @if($ay->status !== 'active')
                                    <form method="POST" action="{{ route('admin.academic-years.activate', $ay->id) }}" class="inline-block">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-extrabold bg-[#FFC107] text-[#3B060F] rounded-xl hover:bg-amber-400 transition shadow-2xs">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Activate
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('admin.academic-years.edit', $ay->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-extrabold bg-slate-800 text-white rounded-xl hover:bg-slate-900 transition shadow-2xs">
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
                                No academic years recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
            {{ $academicYears->links() }}
        </div>
    </div>
</x-app-layout>
