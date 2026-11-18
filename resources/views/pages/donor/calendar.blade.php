<x-layouts.donor title="Donation Schedule Calendar — DonorLink UG">
    <div
        class="space-y-6 sm:space-y-8"
        x-data="{
            view: 'grid',
            selectedDate: '{{ $todayDate }}',
            daysData: {{ Js::from($days) }},
            get selectedDay() {
                return this.daysData.find(d => d.date === this.selectedDate) || this.daysData[0];
            }
        }"
    >
        {{-- Header & Month Navigation Bar --}}
        <div class="bg-white rounded-2xl border border-neutral-200/90 shadow-xs p-6 sm:p-8">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="space-y-1">
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-neutral-900 font-sans">
                        Donation Drives Calendar
                    </h1>
                    <p class="text-sm text-neutral-500 max-w-2xl leading-relaxed">
                        Explore upcoming voluntary blood donation camps across Uganda and track your booked donation sessions with zero wait time.
                    </p>
                </div>

                {{-- Month Navigator & Actions --}}
                <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                    {{-- Month Navigator --}}
                    <div class="inline-flex items-center rounded-xl border border-neutral-300 bg-white p-1 shadow-2xs">
                        <a
                            href="{{ route('donor.calendar', ['month' => $prevMonth, 'filter' => $filter]) }}"
                            class="p-2 rounded-lg text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100 transition-colors cursor-pointer"
                            title="Previous Month"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>

                        <span class="px-3 text-sm font-bold text-neutral-900 whitespace-nowrap min-w-[130px] text-center">
                            {{ $monthLabel }}
                        </span>

                        <a
                            href="{{ route('donor.calendar', ['month' => $nextMonth, 'filter' => $filter]) }}"
                            class="p-2 rounded-lg text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100 transition-colors cursor-pointer"
                            title="Next Month"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>

                    {{-- Jump to Current Month ("Today") --}}
                    <a
                        href="{{ route('donor.calendar', ['filter' => $filter]) }}"
                        class="px-3 py-2 text-xs font-semibold rounded-xl bg-white border border-neutral-300 text-neutral-700 hover:bg-neutral-50 shadow-2xs transition-colors"
                    >
                        Today
                    </a>

                    <x-button :href="route('drives.index')" variant="primary" size="md" class="shadow-sm">
                        <svg class="w-4 h-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Search Drives
                    </x-button>
                </div>
            </div>
        </div>

        {{-- Month Highlights Strip --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-4 rounded-xl border border-neutral-200/90 shadow-2xs flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-primary-50 text-primary-700 flex items-center justify-center font-bold shrink-0">
                    <svg class="w-5 h-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wider block">Drives in {{ $monthLabel }}</span>
                    <p class="text-xl font-bold text-neutral-900">{{ $monthStats['total_drives'] }} Blood Drives</p>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-neutral-200/90 shadow-2xs flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold shrink-0">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wider block">Open for Booking</span>
                    <p class="text-xl font-bold text-emerald-700">{{ $monthStats['drives_with_open_slots'] }} with Available Slots</p>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-neutral-200/90 shadow-2xs flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-700 flex items-center justify-center font-bold shrink-0">
                    <svg class="w-5 h-5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </div>
                <div>
                    <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wider block">Your Booked Sessions</span>
                    <p class="text-xl font-bold text-primary-700">
                        {{ $monthStats['my_appointments_count'] }} {{ $monthStats['my_appointments_count'] === 1 ? 'Appointment' : 'Appointments' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Toolbar: Filters & View Switcher --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-3.5 rounded-xl border border-neutral-200 shadow-2xs">
            {{-- Filter Chips --}}
            <div class="inline-flex rounded-xl border border-neutral-200 p-1 bg-neutral-50 text-xs font-semibold self-start sm:self-auto">
                <a
                    href="{{ route('donor.calendar', ['month' => $month, 'filter' => 'all']) }}"
                    class="px-3 py-1.5 rounded-lg transition-colors {{ $filter === 'all' ? 'bg-white text-primary-700 shadow-2xs font-bold' : 'text-neutral-600 hover:text-neutral-900' }}"
                >
                    All Drives
                </a>
                <a
                    href="{{ route('donor.calendar', ['month' => $month, 'filter' => 'my_appointments']) }}"
                    class="px-3 py-1.5 rounded-lg transition-colors {{ $filter === 'my_appointments' ? 'bg-white text-primary-700 shadow-2xs font-bold' : 'text-neutral-600 hover:text-neutral-900' }}"
                >
                    ⭐ My Appointments ({{ $monthStats['my_appointments_count'] }})
                </a>
            </div>

            {{-- View Switcher --}}
            <div class="inline-flex rounded-lg border border-neutral-200 p-0.5 bg-neutral-100 text-xs font-semibold self-start sm:self-auto">
                <button
                    type="button"
                    x-on:click="view = 'grid'"
                    :class="view === 'grid' ? 'bg-white text-neutral-900 shadow-2xs' : 'text-neutral-600 hover:text-neutral-900'"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-md transition-colors cursor-pointer"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span>Month Grid</span>
                </button>
                <button
                    type="button"
                    x-on:click="view = 'agenda'"
                    :class="view === 'agenda' ? 'bg-white text-neutral-900 shadow-2xs' : 'text-neutral-600 hover:text-neutral-900'"
                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-md transition-colors cursor-pointer"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                    </svg>
                    <span>Schedule List</span>
                </button>
            </div>
        </div>

        {{-- 1. Monthly Calendar Grid --}}
        <div x-show="view === 'grid'" class="space-y-6">
            <div class="bg-white rounded-2xl border border-neutral-200/90 shadow-sm overflow-hidden">
                {{-- Day-of-Week Column Headers (Mon - Sun) --}}
                <div class="grid grid-cols-7 border-b border-neutral-200 bg-neutral-50/90 text-center text-xs font-bold text-neutral-700 py-3 select-none" style="display: grid; grid-template-columns: repeat(7, minmax(0, 1fr));">
                    <div class="py-1"><span class="hidden sm:inline">Monday</span><span class="sm:hidden">Mon</span></div>
                    <div class="py-1"><span class="hidden sm:inline">Tuesday</span><span class="sm:hidden">Tue</span></div>
                    <div class="py-1"><span class="hidden sm:inline">Wednesday</span><span class="sm:hidden">Wed</span></div>
                    <div class="py-1"><span class="hidden sm:inline">Thursday</span><span class="sm:hidden">Thu</span></div>
                    <div class="py-1"><span class="hidden sm:inline">Friday</span><span class="sm:hidden">Fri</span></div>
                    <div class="py-1 text-primary-800 bg-primary-50/40 rounded-sm mx-0.5"><span class="hidden sm:inline">Saturday</span><span class="sm:hidden">Sat</span></div>
                    <div class="py-1 text-primary-800 bg-primary-50/40 rounded-sm mx-0.5"><span class="hidden sm:inline">Sunday</span><span class="sm:hidden">Sun</span></div>
                </div>

                {{-- 7-Day Grid Cells --}}
                <div class="grid grid-cols-7 bg-neutral-200 gap-px" style="display: grid; grid-template-columns: repeat(7, minmax(0, 1fr));">
                    @foreach($days as $day)
                        <div
                            x-on:click="selectedDate = '{{ $day['date'] }}'"
                            :class="{
                                'ring-2 ring-primary-600 ring-inset bg-primary-50/30 z-10': selectedDate === '{{ $day['date'] }}',
                                'bg-white hover:bg-neutral-50/90': {{ $day['isCurrentMonth'] ? 'true' : 'false' }} && selectedDate !== '{{ $day['date'] }}',
                                'bg-neutral-50/70 text-neutral-400': !{{ $day['isCurrentMonth'] ? 'true' : 'false' }} && selectedDate !== '{{ $day['date'] }}'
                            }"
                            class="min-h-[110px] sm:min-h-[135px] p-2 transition-all cursor-pointer flex flex-col justify-between group relative"
                        >
                            {{-- Day Header --}}
                            <div class="flex items-center justify-between">
                                @if($day['isToday'])
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-primary-600 text-white font-bold text-xs shadow-xs">
                                        {{ $day['dayNumber'] }}
                                    </span>
                                @else
                                    <span class="text-xs font-semibold {{ $day['isCurrentMonth'] ? 'text-neutral-900' : 'text-neutral-400' }} pl-1">
                                        {{ $day['dayNumber'] }}
                                    </span>
                                @endif

                                @if($day['hasAppointments'])
                                    <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-primary-600 text-white shadow-2xs">
                                        ⭐ My Appt
                                    </span>
                                @endif
                            </div>

                            {{-- Drives Badges for this day --}}
                            <div class="space-y-1 my-1 overflow-hidden">
                                @foreach($day['drives']->take(2) as $drive)
                                    <div
                                        class="p-1.5 rounded-md border text-[11px] leading-tight {{ $drive['has_spots'] ? 'bg-emerald-50 text-emerald-900 border-emerald-200 hover:bg-emerald-100' : 'bg-neutral-100 text-neutral-600 border-neutral-200' }} transition-colors shadow-2xs truncate block"
                                        title="{{ $drive['title'] }} at {{ $drive['venue'] }}"
                                    >
                                        <div class="flex items-center gap-1 truncate font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $drive['has_spots'] ? 'bg-emerald-500' : 'bg-neutral-400' }} shrink-0"></span>
                                            <span class="truncate">{{ $drive['venue'] }}</span>
                                        </div>
                                        <div class="text-[10px] text-neutral-500 truncate mt-0.5">
                                            @if($drive['has_spots'])
                                                <span class="text-emerald-700 font-bold">{{ $drive['spots_left'] }} spots open</span>
                                            @else
                                                <span>Fully booked</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach

                                @if(count($day['drives']) > 2)
                                    <span class="text-[10px] font-bold text-primary-700 pl-1 block">
                                        +{{ count($day['drives']) - 2 }} more
                                    </span>
                                @endif
                            </div>

                            <div class="h-0.5"></div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Selected Day Schedule Panel --}}
            <div class="bg-white rounded-2xl border border-neutral-200/90 shadow-xs p-5 sm:p-6 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-neutral-200">
                    <div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <h2 class="text-base sm:text-lg font-bold text-neutral-900">
                                Schedule for <span x-text="selectedDay.date"></span>
                            </h2>
                            <template x-if="selectedDay.isToday">
                                <x-badge variant="primary" size="sm">Today</x-badge>
                            </template>
                        </div>
                        <p class="text-xs text-neutral-500 mt-0.5">
                            <span x-text="selectedDay.drives.length"></span> official UBTS donation camp(s) scheduled
                        </p>
                    </div>

                    <a
                        href="{{ route('drives.index') }}"
                        class="text-xs font-semibold text-primary-700 hover:text-primary-900 transition-colors"
                    >
                        Browse all upcoming drives &rarr;
                    </a>
                </div>

                {{-- If user has an appointment on this day --}}
                <template x-if="selectedDay.appointments.length > 0">
                    <div class="p-4 rounded-xl border border-primary-200 bg-primary-50/70 text-primary-900 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
                        <div class="flex items-start gap-3">
                            <div class="w-9 h-9 rounded-lg bg-primary-600 text-white flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <span class="text-xs font-bold uppercase tracking-wider text-primary-800">Your Booked Session</span>
                                <h3 class="text-sm font-bold text-neutral-900">
                                    Appointment Scheduled on this Date
                                </h3>
                                <p class="text-xs text-neutral-600 mt-0.5">
                                    Please arrive 10 minutes early with your national identification or student card.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 self-start sm:self-center shrink-0">
                            <template x-for="apt in selectedDay.appointments" :key="apt.id">
                                <div class="flex items-center gap-2">
                                    <a
                                        :href="`{{ url('/donor/calendar/appointment') }}/${apt.id}/ics`"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white border border-neutral-300 text-neutral-800 hover:bg-neutral-50 transition-colors shadow-2xs"
                                        title="Download Calendar (.ics) invite"
                                    >
                                        <svg class="w-3.5 h-3.5 text-neutral-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        <span>Add to Calendar</span>
                                    </a>
                                    <a
                                        href="{{ route('donor.appointments') }}"
                                        class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-primary-600 text-white hover:bg-primary-700 transition-colors shadow-2xs"
                                    >
                                        Manage
                                    </a>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                {{-- Drives List for Selected Day --}}
                <template x-if="selectedDay.drives.length === 0">
                    <div class="py-8 text-center rounded-xl bg-neutral-50 border border-neutral-100">
                        <svg class="w-8 h-8 text-neutral-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-sm font-semibold text-neutral-800">No donation drives scheduled</p>
                        <p class="text-xs text-neutral-500 mt-0.5">Please check neighboring dates or browse all upcoming blood drives.</p>
                    </div>
                </template>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <template x-for="drive in selectedDay.drives" :key="drive.id">
                        <div class="p-4 rounded-xl border border-neutral-200 bg-white hover:border-neutral-300 transition-all shadow-2xs flex flex-col justify-between space-y-3">
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between gap-2">
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-bold"
                                        :class="drive.has_spots ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-neutral-100 text-neutral-700 border border-neutral-200'"
                                        x-text="drive.has_spots ? `${drive.spots_left} Slots Available` : 'Fully Booked'"
                                    ></span>

                                    <template x-if="drive.time_range">
                                        <span class="text-xs font-semibold text-neutral-600 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span x-text="drive.time_range"></span>
                                        </span>
                                    </template>
                                </div>

                                <h3 class="text-base font-bold text-neutral-900 leading-snug" x-text="drive.title"></h3>

                                <p class="text-xs text-neutral-600 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-neutral-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    </svg>
                                    <span class="font-medium text-neutral-800" x-text="drive.venue"></span>
                                </p>
                                <template x-if="drive.address">
                                    <p class="text-xs text-neutral-500 pl-5.5" x-text="drive.address"></p>
                                </template>
                            </div>

                            <div class="pt-3 border-t border-neutral-100 flex items-center justify-between">
                                <span class="text-xs text-neutral-500" x-text="`${drive.booked} donors registered`"></span>

                                <a
                                    :href="`{{ url('/drives') }}/${drive.id}`"
                                    class="inline-flex items-center justify-center px-4 py-2 text-xs font-semibold rounded-lg bg-primary-600 text-white hover:bg-primary-700 transition-colors shadow-2xs"
                                >
                                    Book Time Slot &rarr;
                                </a>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- 2. Agenda / List View --}}
        <div x-show="view === 'agenda'" class="bg-white rounded-2xl border border-neutral-200/90 shadow-xs overflow-hidden">
            <div class="p-5 sm:p-6 border-b border-neutral-200 flex items-center justify-between">
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-neutral-900">Schedule Agenda for {{ $monthLabel }}</h2>
                    <p class="text-xs text-neutral-500 mt-0.5">Chronological list of all collection drives scheduled this month</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-neutral-100 text-neutral-700">
                    {{ count($agendaDrives) }} Drives Found
                </span>
            </div>

            @if($agendaDrives->isEmpty())
                <div class="p-12 text-center space-y-2">
                    <p class="text-sm font-semibold text-neutral-800">No scheduled drives for {{ $monthLabel }}</p>
                    <p class="text-xs text-neutral-500">Please check other months or search available drives across regions.</p>
                </div>
            @else
                <div class="divide-y divide-neutral-100">
                    @foreach($agendaDrives as $drive)
                        <div class="p-5 sm:p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:bg-neutral-50/60 transition-colors">
                            <div class="flex items-start gap-4">
                                {{-- Date Badge Box --}}
                                <div class="w-14 h-14 rounded-xl bg-primary-50 text-primary-900 border border-primary-200/60 flex flex-col items-center justify-center shrink-0 shadow-2xs">
                                    <span class="text-[10px] uppercase font-bold tracking-wider text-primary-700">{{ $drive['starts_on']->format('M') }}</span>
                                    <span class="text-xl font-extrabold leading-none">{{ $drive['starts_on']->format('d') }}</span>
                                </div>

                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <x-badge :variant="$drive['has_spots'] ? 'success' : 'neutral'" size="sm">
                                            {{ $drive['has_spots'] ? $drive['spots_left'] . ' Slots Available' : 'Fully Booked' }}
                                        </x-badge>
                                    </div>

                                    <h3 class="text-base font-bold text-neutral-900 leading-snug">
                                        {{ $drive['title'] }}
                                    </h3>

                                    <p class="text-xs text-neutral-600 flex flex-wrap items-center gap-x-3 gap-y-1">
                                        <span class="font-medium text-neutral-800">{{ $drive['venue'] }}</span>
                                        @if($drive['address'])
                                            <span>&bull; {{ $drive['address'] }}</span>
                                        @endif
                                        @if($drive['time_range'])
                                            <span class="font-semibold text-primary-800">&bull; {{ $drive['time_range'] }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 self-start md:self-center shrink-0">
                                <a
                                    href="{{ route('drives.show', $drive['id']) }}"
                                    class="px-4 py-2 rounded-xl text-xs font-semibold bg-primary-600 text-white hover:bg-primary-700 transition-colors shadow-2xs"
                                >
                                    Book Slot &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts.donor>
