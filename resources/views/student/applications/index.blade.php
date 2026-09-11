<x-app-layout title="My Applications - CSU–Lal-lo">
    
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900">My Scholarship Applications</h2>
            <p class="text-xs text-slate-500">Track application progress, admin remarks, and document verification status.</p>
        </div>
    </div>

    <!-- Applications List Table -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5">Scholarship Program</th>
                        <th class="px-6 py-3.5">Provider</th>
                        <th class="px-6 py-3.5">Submitted Date</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5">Admin Remarks</th>
                        <th class="px-6 py-3.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($applications as $app)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 font-bold text-slate-900">
                                {{ $app->scholarship->name }}
                            </td>
                            <td class="px-6 py-4 text-slate-600 font-semibold">
                                {{ $app->scholarship->provider }}
                            </td>
                            <td class="px-6 py-4 text-slate-500 font-medium">
                                {{ $app->submitted_at ? $app->submitted_at->format('M d, Y') : $app->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold capitalize 
                                    @if($app->status === 'approved') bg-emerald-100 text-emerald-800 border border-emerald-300
                                    @elseif($app->status === 'rejected') bg-red-100 text-red-800 border border-red-300
                                    @elseif($app->status === 'incomplete') bg-rose-100 text-rose-800 border border-rose-300
                                    @elseif($app->status === 'under_review') bg-blue-100 text-blue-800 border border-blue-300
                                    @else bg-amber-100 text-amber-800 border border-amber-300 @endif">
                                    {{ str_replace('_', ' ', $app->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-500 italic max-w-xs truncate">
                                {{ $app->remarks ?? 'No remarks' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('student.applications.show', $app->id) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-[#3B060F] text-white font-extrabold rounded-xl text-xs hover:bg-[#6B0F1A] transition shadow-xs">
                                    <svg class="w-3.5 h-3.5 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                <p class="font-bold text-sm text-slate-700">No Applications Submitted</p>
                                <p class="text-xs text-slate-400 mt-1">Browse available scholarships and start your first application.</p>
                                <a href="{{ route('student.scholarships.index') }}" class="mt-3 inline-block px-4 py-2 bg-[#FFC107] text-[#3B060F] font-extrabold rounded-xl text-xs hover:bg-amber-400 transition">
                                    Browse Scholarships
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
            {{ $applications->links() }}
        </div>
    </div>

</x-app-layout>
