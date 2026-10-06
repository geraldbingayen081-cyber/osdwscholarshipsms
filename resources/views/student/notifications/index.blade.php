<x-app-layout title="My Notifications - CSU Lal-lo Student">

    <!-- Header Section -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Notifications & Alerts</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">
                Stay updated with scholarship application progress, welfare case assessments, and official referrals.
            </p>
        </div>

        @if($unreadCount > 0)
            <form method="POST" action="{{ route('student.notifications.mark-all-read') }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#3B060F] text-white font-extrabold text-xs rounded-xl hover:bg-[#6B0F1A] transition shadow-xs cursor-pointer">
                    <svg class="w-3.5 h-3.5 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Mark All as Read ({{ $unreadCount }})
                </button>
            </form>
        @endif
    </div>

    <!-- Category Filter Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 mb-6 border-b border-slate-200 dark:border-slate-800">
        <a href="{{ route('student.notifications.index', ['tab' => 'all']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ ($tab ?? 'all') === 'all' ? 'bg-[#3B060F] text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700' }}">
            All ({{ $totalCount }})
        </a>

        <a href="{{ route('student.notifications.index', ['tab' => 'unread']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap flex items-center gap-1.5 {{ ($tab ?? 'all') === 'unread' ? 'bg-[#3B060F] text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700' }}">
            Unread
            @if($unreadCount > 0)
                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-black {{ ($tab ?? 'all') === 'unread' ? 'bg-[#FFC107] text-[#3B060F]' : 'bg-red-600 text-white' }}">{{ $unreadCount }}</span>
            @endif
        </a>

        <a href="{{ route('student.notifications.index', ['tab' => 'scholarships']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap flex items-center gap-1.5 {{ ($tab ?? 'all') === 'scholarships' ? 'bg-[#3B060F] text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700' }}">
            <svg class="w-3.5 h-3.5 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
            </svg>
            Scholarship Updates ({{ $scholarshipCount }})
        </a>

        <a href="{{ route('student.notifications.index', ['tab' => 'welfare']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap flex items-center gap-1.5 {{ ($tab ?? 'all') === 'welfare' ? 'bg-[#3B060F] text-white shadow-xs' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-700' }}">
            <svg class="w-3.5 h-3.5 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
            Welfare Concerns ({{ $welfareCount }})
        </a>
    </div>

    <!-- Notifications List Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xs border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="divide-y divide-slate-100 dark:divide-slate-800">
            @forelse($notifications as $notification)
                @php
                    $isUnread = is_null($notification->read_at);
                    $data = $notification->data;
                    $notifType = $data['type'] ?? 'general';
                    $isWelfare = str_contains($notifType, 'welfare') || str_contains($notification->type, 'Welfare');
                    $isReferral = $notifType === 'welfare_referral';
                @endphp
                <div class="p-5 flex flex-col sm:flex-row sm:items-start justify-between gap-4 transition {{ $isUnread ? 'bg-amber-50/40 dark:bg-amber-950/20' : 'bg-white dark:bg-slate-900' }}">
                    <div class="flex items-start space-x-3.5 flex-1">
                        <!-- Icon Badge -->
                        @if($isReferral)
                            <div class="h-10 w-10 rounded-xl flex items-center justify-center shrink-0 mt-0.5 bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 shadow-xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        @elseif($isWelfare)
                            <div class="h-10 w-10 rounded-xl flex items-center justify-center shrink-0 mt-0.5 bg-indigo-100 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 shadow-xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </div>
                        @else
                            <div class="h-10 w-10 rounded-xl flex items-center justify-center shrink-0 mt-0.5 {{ $isUnread ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-400 border border-amber-200 dark:border-amber-800' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700' }} shadow-xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </div>
                        @endif

                        <div class="space-y-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                @if($isReferral)
                                    <span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-900/50 text-emerald-800 dark:text-emerald-300 font-extrabold text-[10px] rounded-md uppercase tracking-wider">
                                        Scholarship Referral
                                    </span>
                                @elseif($isWelfare)
                                    <span class="px-2 py-0.5 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-800 dark:text-indigo-300 font-extrabold text-[10px] rounded-md uppercase tracking-wider">
                                        Welfare Case
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-extrabold text-[10px] rounded-md uppercase tracking-wider">
                                        Scholarship
                                    </span>
                                @endif

                                @if($isUnread)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-black bg-amber-500 text-white uppercase tracking-wider">
                                        New
                                    </span>
                                @endif

                                <span class="text-[11px] text-slate-400 dark:text-slate-500 font-medium">
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <p class="text-xs text-slate-800 dark:text-slate-200 leading-relaxed font-semibold">
                                {{ $data['message'] ?? 'Notification received.' }}
                            </p>

                            @if(!empty($data['referral_note']))
                                <p class="text-[11px] text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/80 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 font-medium mt-1.5">
                                    <strong class="text-slate-700 dark:text-slate-200">OSDW Note:</strong> {{ $data['referral_note'] }}
                                </p>
                            @elseif(!empty($data['requested_information']))
                                <p class="text-[11px] text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/80 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 font-medium mt-1.5">
                                    <strong class="text-slate-700 dark:text-slate-200">Staff Note:</strong> {{ $data['requested_information'] }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Action Link -->
                    <div class="flex items-center space-x-2 shrink-0 self-end sm:self-center">
                        @if(isset($data['url']))
                            <form method="POST" action="{{ route('student.notifications.read', $notification->id) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 {{ $isReferral ? 'bg-emerald-700 hover:bg-emerald-800 text-white' : 'bg-[#3B060F] hover:bg-[#6B0F1A] text-white' }} font-extrabold text-xs rounded-xl transition shadow-xs cursor-pointer">
                                    @if($isReferral)
                                        <svg class="w-3.5 h-3.5 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                        </svg>
                                        {{ $data['action_label'] ?? 'Apply Now' }}
                                    @else
                                        <svg class="w-3.5 h-3.5 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        {{ $data['action_label'] ?? 'View Details' }}
                                    @endif
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-slate-500 dark:text-slate-400 text-xs">
                    <svg class="w-12 h-12 text-slate-300 dark:text-slate-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    No notifications found in this category.
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>

</x-app-layout>
