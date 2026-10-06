<x-app-layout title="System Logs & Audit Trail - CSU Lal-lo Admin">

    <div x-data="{
        detailModal: false,
        purgeModal: false,
        activeLog: null,
        timeframe: '{{ $timeframe ?? 'all' }}',
        inspectLog(log) {
            this.activeLog = log;
            this.detailModal = true;
        }
    }">

        <!-- Page Header -->
        <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                    <span class="p-2 rounded-xl bg-[#3B060F] text-[#FFC107] shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </span>
                    System Logs & Audit Trail
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">
                    Monitor system events, administrator activities, authentication attempts, workflow audits, and security records.
                </p>
            </div>

            <!-- Header Actions: Export CSV & Purge Logs -->
            <div class="flex items-center gap-2.5">
                <!-- Retention Purge Trigger -->
                <button type="button" 
                        @click="purgeModal = true"
                        class="px-3.5 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-red-50 hover:text-red-700 dark:hover:bg-red-950/40 text-slate-600 dark:text-slate-300 font-bold text-xs rounded-xl border border-slate-200 dark:border-slate-700 transition inline-flex items-center gap-1.5 cursor-pointer shadow-xs">
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Retention Purge
                </button>
            </div>
        </div>

        <!-- Metric Summary Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Total Logs Recorded -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center space-x-3.5">
                <div class="p-3 bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-400 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total System Logs</span>
                    <p class="text-xl font-black text-slate-900 dark:text-white mt-0.5">{{ number_format($stats['total']) }}</p>
                </div>
            </div>

            <!-- Today's Activity -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center space-x-3.5">
                <div class="p-3 bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Today's Activity</span>
                    <p class="text-xl font-black text-emerald-600 dark:text-emerald-400 mt-0.5">{{ number_format($stats['today']) }}</p>
                </div>
            </div>

            <!-- Authentication Events -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center space-x-3.5">
                <div class="p-3 bg-purple-50 dark:bg-purple-950/60 text-purple-700 dark:text-purple-400 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                </div>
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Auth & Access</span>
                    <p class="text-xl font-black text-slate-900 dark:text-white mt-0.5">{{ number_format($stats['auth']) }}</p>
                </div>
            </div>

            <!-- Operational & Management -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-xs flex items-center space-x-3.5">
                <div class="p-3 bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Management Audits</span>
                    <p class="text-xl font-black text-slate-900 dark:text-white mt-0.5">{{ number_format($stats['management']) }}</p>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar Card -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-xs mb-6">
            <form method="GET" action="{{ route('admin.system-logs.index') }}" class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
                    
                    <!-- Search Input (4 cols) -->
                    <div class="lg:col-span-4">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">
                            Search Query
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   name="search" 
                                   value="{{ $search }}" 
                                   placeholder="Search keyword, actor, email, IP, action..." 
                                   class="w-full pl-9 pr-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-[#3B060F] outline-none">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Category / Log Type (3 cols) -->
                    <div class="lg:col-span-3">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1">
                            Log Category
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
                    <div class="lg:col-span-3">
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
                    <div class="lg:col-span-2 flex items-end gap-2">
                        <button type="submit" 
                                class="flex-1 py-2 px-3 bg-[#3B060F] hover:bg-[#500A15] text-[#FFC107] font-bold text-xs rounded-xl transition shadow-xs text-center cursor-pointer">
                            Filter
                        </button>
                        <a href="{{ route('admin.system-logs.index') }}" 
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

        <!-- System Logs Table Card -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-xs overflow-hidden">
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-100 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 text-[11px] font-bold uppercase tracking-wider text-slate-700 dark:text-slate-200">
                            <th class="py-3.5 px-4">Timestamp</th>
                            <th class="py-3.5 px-4">Actor / User</th>
                            <th class="py-3.5 px-4">Category</th>
                            <th class="py-3.5 px-4">Action</th>
                            <th class="py-3.5 px-4">Activity Description</th>
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
                                    'Settings' => 'bg-slate-100 text-slate-800 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
                                    'Export' => 'bg-teal-50 text-teal-800 border-teal-200 dark:bg-teal-950/50 dark:text-teal-300 dark:border-teal-800',
                                    'Security' => 'bg-red-50 text-red-800 border-red-200 dark:bg-red-950/50 dark:text-red-300 dark:border-red-800',
                                ];
                                $badgeStyle = $typeBadges[$log->log_type] ?? 'bg-slate-100 text-slate-800 border-slate-200';
                            @endphp
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                                <!-- Timestamp -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="font-bold text-slate-800 dark:text-slate-200 block">
                                        {{ $log->created_at->format('M d, Y') }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-medium">
                                        {{ $log->created_at->format('h:i:s A') }} ({{ $log->created_at->diffForHumans() }})
                                    </span>
                                </td>

                                <!-- User / Actor -->
                                <td class="py-3.5 px-4">
                                    @if($log->user)
                                        <div class="flex items-center space-x-2">
                                            <div class="h-6 w-6 rounded-full bg-[#3B060F] text-[#FFC107] text-[10px] font-black flex items-center justify-center shrink-0">
                                                {{ strtoupper(substr($log->user->first_name, 0, 1) . substr($log->user->last_name, 0, 1)) }}
                                            </div>
                                            <div class="overflow-hidden max-w-[150px]">
                                                <p class="font-bold text-slate-900 dark:text-white truncate" title="{{ $log->user->full_name }}">
                                                    {{ $log->user->full_name }}
                                                </p>
                                                <span class="text-[10px] text-slate-400 block truncate">{{ $log->user->email }}</span>
                                            </div>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-500">
                                            System / Guest
                                        </span>
                                    @endif
                                </td>

                                <!-- Log Category -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-extrabold border {{ $badgeStyle }}">
                                        {{ $log->log_type }}
                                    </span>
                                </td>

                                <!-- Action Code -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <code class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-[11px] font-mono font-bold">
                                        {{ $log->action }}
                                    </code>
                                </td>

                                <!-- Description -->
                                <td class="py-3.5 px-4 max-w-sm">
                                    <p class="text-slate-800 dark:text-slate-200 font-medium leading-relaxed">
                                        {{ $log->description }}
                                    </p>
                                    @if($log->subject_type && $log->subject_id)
                                        <span class="text-[10px] text-slate-400 block mt-0.5">
                                            Entity: <strong class="text-slate-600 dark:text-slate-300">{{ class_basename($log->subject_type) }} #{{ $log->subject_id }}</strong>
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center">
                                    <div class="max-w-sm mx-auto space-y-2">
                                        <div class="h-12 w-12 bg-slate-100 dark:bg-slate-800 text-slate-400 rounded-full flex items-center justify-center mx-auto">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <p class="text-sm font-extrabold text-slate-800 dark:text-slate-200">No System Logs Found</p>
                                        <p class="text-xs text-slate-400 font-medium">No activity log entries match your selected criteria or search term.</p>
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

        <!-- Alpine.js Audit Log Detail Inspection Modal -->
        <div x-show="detailModal" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.away="detailModal = false" 
                 class="bg-white dark:bg-slate-900 rounded-2xl max-w-2xl w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-5">
                
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
                                Audit Log #<span x-text="activeLog?.id"></span>
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
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Description</span>
                        <p class="font-semibold text-slate-800 dark:text-slate-200 leading-relaxed" x-text="activeLog?.description"></p>
                    </div>

                    <!-- Meta Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Category</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200 mt-0.5 block" x-text="activeLog?.log_type"></span>
                        </div>
                        <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Action</span>
                            <code class="font-mono font-bold text-slate-800 dark:text-slate-200 mt-0.5 block" x-text="activeLog?.action"></code>
                        </div>
                        <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Actor</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200 mt-0.5 block" x-text="activeLog?.user_name"></span>
                        </div>
                        <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Target Entity</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200 mt-0.5 block">
                                <span x-text="activeLog?.subject_type"></span> (<span x-text="activeLog?.subject_id"></span>)
                            </span>
                        </div>
                        <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">IP Address</span>
                            <code class="font-mono text-slate-800 dark:text-slate-200 mt-0.5 block" x-text="activeLog?.ip_address"></code>
                        </div>
                        <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Actor Role</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200 mt-0.5 block" x-text="activeLog?.user_role"></span>
                        </div>
                    </div>

                    <!-- User Agent -->
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">User Agent / Client Device</span>
                        <div class="p-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-xl border border-slate-100 dark:border-slate-800 font-mono text-[11px] text-slate-600 dark:text-slate-400 break-all" x-text="activeLog?.user_agent"></div>
                    </div>

                    <!-- JSON Properties / Metadata -->
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Payload Metadata & Changes (JSON)</span>
                        <pre class="p-3 bg-slate-900 text-emerald-400 rounded-xl text-[11px] font-mono overflow-x-auto max-h-48 border border-slate-800" x-text="JSON.stringify(activeLog?.properties, null, 2)"></pre>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" @click="detailModal = false" class="px-5 py-2 bg-[#3B060F] text-[#FFC107] font-extrabold text-xs rounded-xl hover:bg-[#500A15] transition cursor-pointer">
                        Close Audit Inspector
                    </button>
                </div>
            </div>
        </div>

        <!-- Retention Purge Confirmation Modal -->
        <div x-show="purgeModal" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.away="purgeModal = false" 
                 class="bg-white dark:bg-slate-900 rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-4">
                
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="font-extrabold text-slate-900 dark:text-white text-base flex items-center gap-2">
                        <span class="p-1 rounded-lg bg-red-100 dark:bg-red-950 text-red-600 dark:text-red-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </span>
                        Log Retention Purge
                    </h3>
                    <button type="button" @click="purgeModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                    To maintain database performance and fulfill data retention compliance, select the age threshold of historical audit logs to purge. <strong>This action cannot be undone.</strong>
                </p>

                <form method="POST" action="{{ route('admin.system-logs.clear-old') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            Purge logs older than:
                        </label>
                        <select name="days" class="w-full py-2.5 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-red-500 outline-none">
                            <option value="365">Older than 1 Year (365 Days)</option>
                            <option value="180" selected>Older than 6 Months (180 Days)</option>
                            <option value="90">Older than 3 Months (90 Days)</option>
                            <option value="60">Older than 60 Days</option>
                            <option value="30">Older than 30 Days</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <button type="button" @click="purgeModal = false" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-xl hover:bg-slate-200 transition cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-extrabold text-xs rounded-xl transition shadow-xs cursor-pointer">
                            Confirm Purge
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</x-app-layout>
