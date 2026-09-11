<x-app-layout title="My Notifications - CSU Lal-lo Student">

    <!-- Header Section -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Notifications & Alerts</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">
                Stay updated with your scholarship application status changes and document resubmission notices.
            </p>
        </div>

        @if($unreadCount > 0)
            <form method="POST" action="{{ route('student.notifications.mark-all-read') }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#3B060F] text-white font-extrabold text-xs rounded-xl hover:bg-[#6B0F1A] transition shadow-xs">
                    <svg class="w-3.5 h-3.5 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Mark All as Read
                </button>
            </form>
        @endif
    </div>

    <!-- Notifications List Card -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200 overflow-hidden">
        <div class="divide-y divide-slate-100">
            @forelse($notifications as $notification)
                @php
                    $isUnread = is_null($notification->read_at);
                    $data = $notification->data;
                @endphp
                <div class="p-5 flex items-start justify-between transition {{ $isUnread ? 'bg-amber-50/40 font-medium' : 'bg-white' }}">
                    <div class="flex items-start space-x-3.5">
                        <div class="h-9 w-9 rounded-xl flex items-center justify-center shrink-0 mt-0.5 {{ $isUnread ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-500' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>

                        <div>
                            <p class="text-xs text-slate-800 leading-relaxed font-semibold">
                                {{ $data['message'] ?? 'Notification received.' }}
                            </p>
                            <span class="text-[10px] text-slate-400 font-medium mt-1 block">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3 shrink-0 ml-4">
                        @if(isset($data['url']))
                            <form method="POST" action="{{ route('student.notifications.read', $notification->id) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#3B060F] text-white font-extrabold text-[11px] rounded-xl hover:bg-[#6B0F1A] transition shadow-xs">
                                    <svg class="w-3.5 h-3.5 text-[#FFC107]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    View Details
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-slate-500 text-xs">
                    <svg class="w-12 h-12 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    You have no notifications at this time.
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>

</x-app-layout>
