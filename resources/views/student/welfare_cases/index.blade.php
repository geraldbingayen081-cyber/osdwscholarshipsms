<x-app-layout title="My Welfare Cases - CSU–Lal-lo">
    
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">My Welfare Cases</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Track reported student concerns, OSDW assessments, and official referrals.</p>
        </div>
        <div>
            <a href="{{ route('student.welfare-cases.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#FFC107] text-[#3B060F] font-extrabold rounded-xl text-xs hover:bg-amber-400 transition shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Report Welfare Concern
            </a>
        </div>
    </div>

    <!-- Welfare Cases List Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-100 dark:bg-slate-800 text-[11px] font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-3.5">Category</th>
                        <th class="px-6 py-3.5">Submitted Date</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5">Referrals</th>
                        <th class="px-6 py-3.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($welfareCases as $case)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                            
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700">
                                    {{ $case->category }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-500 dark:text-slate-400 font-medium">
                                {{ $case->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'Open' => 'bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-800',
                                        'Under Assessment' => 'bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-950/50 dark:text-blue-300 dark:border-blue-800',
                                        'Referred' => 'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800',
                                        'For Follow-up' => 'bg-purple-50 text-purple-800 border-purple-200 dark:bg-purple-950/50 dark:text-purple-300 dark:border-purple-800',
                                        'Resolved' => 'bg-teal-50 text-teal-800 border-teal-200 dark:bg-teal-950/50 dark:text-teal-300 dark:border-teal-800',
                                        'Closed' => 'bg-slate-100 text-slate-800 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
                                    ];
                                    $badgeClass = $statusColors[$case->status] ?? 'bg-slate-100 text-slate-800 border-slate-200';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold border {{ $badgeClass }}">
                                    {{ $case->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($case->referrals->isNotEmpty())
                                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 dark:text-emerald-400">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $case->referrals->first()->recipient_name }}
                                    </span>
                                @else
                                    <span class="text-[11px] text-slate-400 italic">None</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('student.welfare-cases.show', $case->id) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-[#3B060F] text-white font-extrabold rounded-xl text-xs hover:bg-[#6B0F1A] transition shadow-xs">
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
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                                <p class="font-bold text-sm text-slate-700 dark:text-slate-300">No Welfare Cases Found</p>
                                <p class="text-xs text-slate-400 mt-1">You haven't reported any student welfare concerns.</p>
                                <a href="{{ route('student.welfare-cases.create') }}" class="mt-3 inline-block px-4 py-2 bg-[#FFC107] text-[#3B060F] font-extrabold rounded-xl text-xs hover:bg-amber-400 transition shadow-xs">
                                    Report a Concern
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($welfareCases->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50">
                {{ $welfareCases->links() }}
            </div>
        @endif
    </div>

</x-app-layout>
