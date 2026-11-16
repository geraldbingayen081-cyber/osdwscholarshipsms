<x-app-layout title="My Activity Log - CSU–Lal-lo">

    <div x-data="{
        detailModal: false,
        activeLog: null,
        timeframe: '{{ $timeframe ?? 'all' }}',
        inspectLog(log) {
            this.activeLog = log;
            this.detailModal = true;
        }
    }">

        <!-- Page Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                    <span class="p-2 rounded-xl bg-[#3B060F] text-[#FFC107] shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                    Activity Log & Account History
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">
                    Track your account sign-ins, scholarship application submissions, document uploads, compliance updates, and security events.
                </p>
            </div>
        </div>

        <!-- Metric Summary Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Total Activities -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center space-x-3.5">
                <div class="p-3 bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-400 rounded-xl shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Activities</span>
                    <p class="text-xl font-black text-slate-900 dark:text-white mt-0.5">{{ number_format($stats['total']) }}</p>
                </div>
            </div>

            <!-- Today's Activity -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center space-x-3.5">
                <div class="p-3 bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 rounded-xl shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Today's Activity</span>
                    <p class="text-xl font-black text-emerald-600 dark:text-emerald-400 mt-0.5">{{ number_format($stats['today']) }}</p>
                </div>
            </div>

            <!-- Scholarship & Application Events -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center space-x-3.5">
                <div class="p-3 bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 rounded-xl shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                    </svg>
                </div>
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Scholarship Events</span>
                    <p class="text-xl font-black text-slate-900 dark:text-white mt-0.5">{{ number_format($stats['applications']) }}</p>
                </div>
            </div>

            <!-- Account & Security Events -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center space-x-3.5">
                <div class="p-3 bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-400 rounded-xl shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Account & Security</span>
                    <p class="text-xl font-black text-slate-900 dark:text-white mt-0.5">{{ number_format($stats['security']) }}</p>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar Card -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-xs mb-6">
            <form method="GET" action="{{ route('student.activity-logs.index') }}" class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">

                    <!-- Search Input (4 cols) -->
                    <div class="sm:col-span-4">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">
                            Search Activity
                        </label>
                        <div class="relative">
                            <input type="text"
                                   name="search"
                                   value="{{ $search }}"
                                   placeholder="Search description, action, or IP address..."
                                   class="w-full pl-9 pr-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-[#3B060F] outline-none">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Category / Log Type (3 cols) -->
                    <div class="sm:col-span-3">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">
                            Activity Category
                        </label>
                        <select name="log_type"
                                class="w-full py-2 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-[#3B060F] outline-none">
                            <option value="">All Categories</option>
                            @foreach($availableTypes as $typeKey => $typeLabel)
                                <option value="{{ $typeKey }}" {{ $logType === $typeKey ? 'selected' : '' }}>
                                    {{ $typeLabel }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Timeframe Select (3 cols) -->
                    <div class="sm:col-span-3">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">
                            Timeframe
                        </label>
                        <select name="timeframe"
                                x-model="timeframe"
                                class="w-full py-2 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-[#3B060F] outline-none">
                            <option value="all">All Recorded Time</option>
                            <option value="today">Today</option>
                            <option value="yesterday">Yesterday</option>
                            <option value="7days">Last 7 Days</option>
                            <option value="30days">Last 30 Days</option>
                            <option value="custom">Custom Date Range</option>
                        </select>
                    </div>

                    <!-- Filter Action Buttons (2 cols) -->
                    <div class="sm:col-span-2 flex items-end gap-2">
                        <button type="submit"
                                class="flex-1 py-2 px-3 bg-[#3B060F] hover:bg-[#500A15] text-[#FFC107] font-bold text-xs rounded-xl transition shadow-xs text-center cursor-pointer">
                            Filter
                        </button>
                        <a href="{{ route('student.activity-logs.index') }}"
                           class="py-2 px-3 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 font-bold text-xs rounded-xl transition text-center"
                           title="Reset Filters">
                            ✕
                        </a>
                    </div>
                </div>

                <!-- Custom Date Range Row (Shown conditionally) -->
                <div x-show="timeframe === 'custom'" x-cloak class="pt-3 border-t border-slate-100 dark:border-slate-800 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">
                            Start Date
                        </label>
                        <input type="date"
                               name="start_date"
                               value="{{ $startDate }}"
                               class="w-full py-1.5 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-[#3B060F] outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">
                            End Date
                        </label>
                        <input type="date"
                               name="end_date"
                               value="{{ $endDate }}"
                               class="w-full py-1.5 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-[#3B060F] outline-none">
                    </div>
                </div>
            </form>
        </div>

        <!-- Activity Logs Table Card -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-700 text-[11px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                            <th class="py-3.5 px-5">Date & Time</th>
                            <th class="py-3.5 px-5">Category</th>
                            <th class="py-3.5 px-5">Action</th>
                            <th class="py-3.5 px-5">Activity Description</th>
                            <th class="py-3.5 px-5">Performed By</th>
                            <th class="py-3.5 px-5 text-right">Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 text-xs">
                        @forelse($logs as $log)
                            @php
                                $typeBadges = [
                                    'Authentication' => 'bg-purple-50 text-purple-800 border-purple-200 dark:bg-purple-950/50 dark:text-purple-300 dark:border-purple-800',
                                    'Scholarship' => 'bg-amber-50 text-amber-800 border-amber-200 dark:bg-amber-950/50 dark:text-amber-300 dark:border-amber-800',
                                    'Application' => 'bg-blue-50 text-blue-800 border-blue-200 dark:bg-blue-950/50 dark:text-blue-300 dark:border-blue-800',
                                    'Welfare Case' => 'bg-rose-50 text-rose-800 border-rose-200 dark:bg-rose-950/50 dark:text-rose-300 dark:border-rose-800',
                                    'Compliance' => 'bg-indigo-50 text-indigo-800 border-indigo-200 dark:bg-indigo-950/50 dark:text-indigo-300 dark:border-indigo-800',
                                    'Scholar' => 'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800',
                                    'Student' => 'bg-teal-50 text-teal-800 border-teal-200 dark:bg-teal-950/50 dark:text-teal-300 dark:border-teal-800',
                                    'Security' => 'bg-red-50 text-red-800 border-red-200 dark:bg-red-950/50 dark:text-red-300 dark:border-red-800',
                                ];
                                $badgeStyle = $typeBadges[$log->log_type] ?? 'bg-slate-100 text-slate-800 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700';

                                $isSelf = $log->user_id === Auth::id();
                                $actorLabel = $isSelf ? 'You' : ($log->user && $log->user->isAdmin() ? 'OSDW Admin' : ($log->user?->full_name ?? 'System'));

                                $logPayload = [
                                    'id' => $log->id,
                                    'log_type' => $log->log_type,
                                    'action' => str_replace('_', ' ', $log->action),
                                    'raw_action' => $log->action,
                                    'description' => $log->description,
                                    'actor' => $actorLabel,
                                    'ip_address' => $log->ip_address ?? 'N/A',
                                    'user_agent' => $log->user_agent ?? 'N/A',
                                    'created_at' => $log->created_at->format('F d, Y h:i:s A'),
                                    'created_at_human' => $log->created_at->diffForHumans(),
                                    'subject_type' => $log->subject_type ? class_basename($log->subject_type) : null,
                                    'subject_id' => $log->subject_id,
                                    'properties' => $log->properties,
                                ];
                            @endphp
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition">
                                <!-- Timestamp -->
                                <td class="py-3.5 px-5 whitespace-nowrap">
                                    <span class="font-bold text-slate-800 dark:text-slate-200 block">
                                        {{ $log->created_at->format('M d, Y') }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-medium">
                                        {{ $log->created_at->format('h:i:s A') }} • {{ $log->created_at->diffForHumans() }}
                                    </span>
                                </td>

                                <!-- Category -->
                                <td class="py-3.5 px-5 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-extrabold border {{ $badgeStyle }}">
                                        {{ $log->log_type }}
                                    </span>
                                </td>

                                <!-- Action -->
                                <td class="py-3.5 px-5 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-[11px] font-bold capitalize">
                                        {{ str_replace('_', ' ', $log->action) }}
                                    </span>
                                </td>

                                <!-- Description -->
                                <td class="py-3.5 px-5 max-w-md">
                                    <p class="text-slate-800 dark:text-slate-200 font-medium leading-relaxed">
                                        {{ $log->description }}
                                    </p>
                                    @if($log->subject_type && $log->subject_id)
                                        <span class="text-[10px] text-slate-400 block mt-0.5">
                                            Record: <strong class="text-slate-600 dark:text-slate-300">{{ class_basename($log->subject_type) }} #{{ $log->subject_id }}</strong>
                                        </span>
                                    @endif
                                </td>

                                <!-- Performed By -->
                                <td class="py-3.5 px-5 whitespace-nowrap">
                                    @if($isSelf)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            You
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                            {{ $actorLabel }}
                                        </span>
                                    @endif
                                </td>

                                <!-- View Details Action -->
                                <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                    <button type="button"
                                            @click="inspectLog(@js($logPayload))"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-[#3B060F] dark:bg-slate-800 dark:hover:bg-[#3B060F] text-slate-700 hover:text-white dark:text-slate-300 dark:hover:text-white font-bold rounded-xl text-[11px] transition cursor-pointer">
                                        <svg class="w-3.5 h-3.5 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Details
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center">
                                    <div class="max-w-sm mx-auto space-y-2">
                                        <div class="h-12 w-12 bg-slate-100 dark:bg-slate-800 text-slate-400 rounded-full flex items-center justify-center mx-auto">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <p class="text-sm font-extrabold text-slate-800 dark:text-slate-200">No Activity Logs Found</p>
                                        <p class="text-xs text-slate-400 font-medium">No activity records match your current filter or search criteria.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Footer -->
            @if($logs->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/30">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>

        <!-- Activity Log Detail Modal -->
        <div x-show="detailModal"
             x-cloak
             @keydown.escape.window="detailModal = false"
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.away="detailModal = false"
                 class="bg-white dark:bg-slate-900 rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-5">

                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3.5">
                    <div class="flex items-center gap-2.5">
                        <span class="p-1.5 rounded-lg bg-[#3B060F] text-[#FFC107]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        <div>
                            <h3 class="font-extrabold text-slate-900 dark:text-white text-base">
                                Activity Record #<span x-text="activeLog?.id"></span>
                            </h3>
                            <span class="text-[11px] text-slate-400 font-medium" x-text="activeLog?.created_at"></span>
                        </div>
                    </div>
                    <button type="button" @click="detailModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Event Overview -->
                <div class="space-y-3 text-xs">
                    <div class="p-3.5 bg-slate-50 dark:bg-slate-800/80 rounded-xl border border-slate-100 dark:border-slate-800">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Activity Description</span>
                        <p class="font-semibold text-slate-800 dark:text-slate-200 leading-relaxed" x-text="activeLog?.description"></p>
                    </div>

                    <!-- Meta Grid -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Category</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200 mt-0.5 block" x-text="activeLog?.log_type"></span>
                        </div>
                        <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Action</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200 mt-0.5 block capitalize" x-text="activeLog?.action"></span>
                        </div>
                        <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Performed By</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200 mt-0.5 block" x-text="activeLog?.actor"></span>
                        </div>
                        <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">IP Address</span>
                            <code class="font-mono text-slate-800 dark:text-slate-200 mt-0.5 block" x-text="activeLog?.ip_address"></code>
                        </div>
                    </div>

                    <!-- Browser / Device -->
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Device / Browser</span>
                        <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-100 dark:border-slate-800 font-mono text-[11px] text-slate-600 dark:text-slate-400 break-all" x-text="activeLog?.user_agent"></div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="detailModal = false" class="px-5 py-2 bg-[#3B060F] text-[#FFC107] font-extrabold text-xs rounded-xl hover:bg-[#500A15] transition cursor-pointer">
                        Close
                    </button>
                </div>
            </div>
        </div>

    </div>

</x-app-layout>

