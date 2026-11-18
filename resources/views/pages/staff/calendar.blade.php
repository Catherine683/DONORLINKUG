<x-layouts.staff title="Drives Schedule Calendar — Staff Portal">
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
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-neutral-200">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-neutral-900">Drives Schedule Calendar</h1>
                    <x-badge variant="primary" size="sm">UBTS Ops</x-badge>
                </div>
                <p class="text-xs sm:text-sm text-neutral-500 mt-1">
                    Uganda Blood Transfusion Services • Comprehensive monthly schedule across mobile camps and fixed collection centers
                </p>
            </div>

            {{-- Top Controls: Month Selector & Filters --}}
            <div class="flex flex-wrap items-center gap-2.5">
                {{-- Month Navigator --}}
                <div class="inline-flex items-center rounded-xl border border-neutral-300 bg-white p-1 shadow-2xs">
                    <a
                        href="{{ route('staff.calendar', ['month' => $prevMonth, 'status' => $statusFilter]) }}"
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
                        href="{{ route('staff.calendar', ['month' => $nextMonth, 'status' => $statusFilter]) }}"
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
                    href="{{ route('staff.calendar', ['status' => $statusFilter]) }}"
                    class="px-3 py-2 text-xs font-semibold rounded-xl bg-white border border-neutral-300 text-neutral-700 hover:bg-neutral-50 shadow-2xs transition-colors"
                >
                    Today
                </a>

                {{-- Status Filter Dropdown --}}
                <form method="GET" action="{{ route('staff.calendar') }}" class="inline-flex items-center">
                    <input type="hidden" name="month" value="{{ $month }}">
                    <select
                        name="status"
                        onchange="this.form.submit()"
                        class="px-3 py-2 text-xs font-semibold rounded-xl bg-white border border-neutral-300 text-neutral-700 hover:border-neutral-400 focus:outline-hidden focus:ring-2 focus:ring-primary-500 shadow-2xs transition-colors cursor-pointer"
                    >
                        <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>All Statuses</option>
                        <option value="published" {{ $statusFilter === 'published' ? 'selected' : '' }}>Published Only</option>
                        <option value="draft" {{ $statusFilter === 'draft' ? 'selected' : '' }}>Draft Only</option>
                        <option value="closed" {{ $statusFilter === 'closed' ? 'selected' : '' }}>Closed Only</option>
                    </select>
                </form>

                {{-- Create Drive Action --}}
                <a
                    href="{{ route('staff.drives.create') }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold rounded-xl bg-primary-600 text-white hover:bg-primary-700 shadow-xs transition-colors"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>New Drive</span>
                </a>
            </div>
        </div>

        {{-- Month Summary Statistics KPI Strip --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white p-4 rounded-xl border border-neutral-200/90 shadow-2xs">
                <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wider block">Drives in Month</span>
                <p class="text-2xl font-bold text-neutral-900 mt-1">{{ $monthStats['total_drives'] }}</p>
                <div class="flex items-center gap-2 mt-1 text-[11px] text-neutral-500">
                    <span class="text-emerald-700 font-semibold">{{ $monthStats['published_drives'] }} published</span>
                    <span>&bull;</span>
                    <span class="text-amber-700 font-semibold">{{ $monthStats['draft_drives'] }} draft</span>
                </div>
            </div>

            <div class="bg-white p-4 rounded-xl border border-neutral-200/90 shadow-2xs">
                <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wider block">Total Slot Capacity</span>
                <p class="text-2xl font-bold text-primary-700 mt-1">{{ $monthStats['total_capacity'] }}</p>
                <span class="text-[11px] text-neutral-500 mt-1 block">Scheduled collection places</span>
            </div>

            <div class="bg-white p-4 rounded-xl border border-neutral-200/90 shadow-2xs">
                <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wider block">Booked Appointments</span>
                <p class="text-2xl font-bold text-neutral-900 mt-1">{{ $monthStats['total_booked'] }}</p>
                <span class="text-[11px] text-neutral-500 mt-1 block">
                    {{ $monthStats['total_capacity'] > 0 ? round(($monthStats['total_booked'] / $monthStats['total_capacity']) * 100, 1) : 0 }}% overall fill rate
                </span>
            </div>

            <div class="bg-white p-4 rounded-xl border border-neutral-200/90 shadow-2xs">
                <span class="text-xs font-semibold text-neutral-500 uppercase tracking-wider block">Completed / Closed</span>
                <p class="text-2xl font-bold text-neutral-600 mt-1">{{ $monthStats['closed_drives'] }}</p>
                <span class="text-[11px] text-neutral-500 mt-1 block">Drives concluded</span>
            </div>
        </div>

        {{-- Toolbar: View Toggle & Color Legend --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-3.5 rounded-xl border border-neutral-200 shadow-2xs">
            {{-- Legend --}}
            <div class="flex flex-wrap items-center gap-4 text-xs font-medium text-neutral-600">
                <span class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-emerald-100"></span>
                    Published (Active)
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 ring-2 ring-amber-100"></span>
                    Draft (Unpublished)
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-neutral-400"></span>
                    Closed
                </span>
                <span class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full border-2 border-primary-600"></span>
                    Today
                </span>
            </div>

            {{-- View Toggle (Grid vs List) --}}
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
                    <span>Agenda List</span>
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
                            {{-- Day Header (Date number & today badge) --}}
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

                                @if($day['hasDrives'])
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold {{ count($day['drives']) > 1 ? 'bg-primary-50 text-primary-700' : 'text-neutral-500' }}">
                                        {{ count($day['drives']) }} {{ count($day['drives']) === 1 ? 'camp' : 'camps' }}
                                    </span>
                                @endif
                            </div>

                            {{-- Drives Badges for this day --}}
                            <div class="space-y-1 my-1 overflow-hidden">
                                @foreach($day['drives']->take(2) as $drive)
                                    @php
                                        $pillClass = match($drive['status']->value) {
                                            'published' => 'bg-emerald-50 text-emerald-900 border-emerald-200 hover:bg-emerald-100',
                                            'draft' => 'bg-amber-50 text-amber-900 border-amber-200 hover:bg-amber-100',
                                            'closed' => 'bg-neutral-100 text-neutral-700 border-neutral-200 hover:bg-neutral-200',
                                            default => 'bg-neutral-100 text-neutral-800 border-neutral-200',
                                        };
                                        $dotClass = match($drive['status']->value) {
                                            'published' => 'bg-emerald-500',
                                            'draft' => 'bg-amber-500',
                                            'closed' => 'bg-neutral-400',
                                            default => 'bg-neutral-400',
                                        };
                                    @endphp
                                    <div
                                        class="p-1.5 rounded-md border text-[11px] leading-tight {{ $pillClass }} transition-colors shadow-2xs truncate block"
                                        title="{{ $drive['title'] }} ({{ $drive['venue'] }})"
                                    >
                                        <div class="flex items-center gap-1 truncate font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }} shrink-0"></span>
                                            <span class="truncate">{{ $drive['title'] }}</span>
                                        </div>
                                        <div class="text-[10px] text-neutral-500 flex items-center justify-between mt-0.5">
                                            <span class="truncate">{{ $drive['venue'] }}</span>
                                            <span class="font-bold shrink-0 ml-1">{{ $drive['booked'] }}/{{ $drive['capacity'] }}</span>
                                        </div>
                                    </div>
                                @endforeach

                                @if(count($day['drives']) > 2)
                                    <span class="text-[10px] font-bold text-primary-700 pl-1 block">
                                        +{{ count($day['drives']) - 2 }} more
                                    </span>
                                @endif
                            </div>

                            {{-- Bottom spacer --}}
                            <div class="h-1"></div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Selected Day Detailed Schedule Panel --}}
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
                            <span x-text="selectedDay.drives.length"></span> blood donation drive(s) scheduled on this date
                        </p>
                    </div>

                    <a
                        href="{{ route('staff.drives.create') }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-neutral-900 text-white hover:bg-neutral-800 transition-colors shadow-2xs self-start sm:self-auto"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Schedule Drive for this Day</span>
                    </a>
                </div>

                {{-- Drives List for Selected Day --}}
                <template x-if="selectedDay.drives.length === 0">
                    <div class="py-8 text-center rounded-xl bg-neutral-50 border border-neutral-100">
                        <svg class="w-8 h-8 text-neutral-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="text-sm font-semibold text-neutral-800">No blood drives scheduled</p>
                        <p class="text-xs text-neutral-500 mt-0.5">There are no collection drives recorded for this date.</p>
                    </div>
                </template>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <template x-for="drive in selectedDay.drives" :key="drive.id">
                        <div class="p-4 rounded-xl border border-neutral-200 bg-white hover:border-neutral-300 transition-all shadow-2xs flex flex-col justify-between space-y-4">
                            <div class="space-y-2">
                                <div class="flex items-center justify-between gap-2">
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wider"
                                        :class="{
                                            'bg-emerald-50 text-emerald-800 border border-emerald-200': drive.status === 'published',
                                            'bg-amber-50 text-amber-800 border border-amber-200': drive.status === 'draft',
                                            'bg-neutral-100 text-neutral-700 border border-neutral-200': drive.status === 'closed'
                                        }"
                                        x-text="drive.status.toUpperCase()"
                                    ></span>

                                    <template x-if="drive.is_live">
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Active Today
                                        </span>
                                    </template>
                                </div>

                                <h3 class="text-base font-bold text-neutral-900 leading-snug" x-text="drive.title"></h3>

                                <div class="space-y-1 text-xs text-neutral-600">
                                    <p class="flex items-center gap-1.5 font-medium">
                                        <svg class="w-4 h-4 text-neutral-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        </svg>
                                        <span x-text="drive.venue"></span>
                                    </p>
                                    <template x-if="drive.address">
                                        <p class="text-neutral-500 pl-5.5 text-[11px]" x-text="drive.address"></p>
                                    </template>
                                    <template x-if="drive.time_range">
                                        <p class="flex items-center gap-1.5 text-neutral-700 font-semibold">
                                            <svg class="w-4 h-4 text-neutral-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span x-text="drive.time_range"></span>
                                        </p>
                                    </template>
                                </div>

                                {{-- Capacity & Fill progress --}}
                                <div class="pt-2">
                                    <div class="flex items-center justify-between text-xs font-semibold mb-1">
                                        <span class="text-neutral-600">Booked / Capacity</span>
                                        <span class="text-neutral-900" x-text="`${drive.booked} / ${drive.capacity} (${drive.fill_rate}%)`"></span>
                                    </div>
                                    <div class="w-full bg-neutral-100 rounded-full h-1.5 overflow-hidden">
                                        <div
                                            class="bg-primary-600 h-1.5 rounded-full"
                                            :style="`width: ${Math.min(100, drive.fill_rate)}%`"
                                        ></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Action buttons --}}
                            <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-neutral-100">
                                <a
                                    :href="`{{ url('/staff/drives') }}/${drive.id}`"
                                    class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold rounded-lg bg-neutral-900 text-white hover:bg-neutral-800 transition-colors shadow-2xs"
                                >
                                    Manage Drive
                                </a>

                                <a
                                    :href="`{{ url('/staff/drives') }}/${drive.id}/roster`"
                                    class="inline-flex items-center justify-center px-3 py-1.5 text-xs font-semibold rounded-lg border border-neutral-300 text-neutral-700 bg-white hover:bg-neutral-50 transition-colors shadow-2xs"
                                >
                                    Live Roster &rarr;
                                </a>

                                <a
                                    :href="`{{ url('/staff/drives') }}/${drive.id}/report`"
                                    class="inline-flex items-center justify-center px-2.5 py-1.5 text-xs font-semibold rounded-lg text-neutral-600 hover:text-neutral-900 hover:bg-neutral-100 transition-colors"
                                    title="Drive Report"
                                >
                                    Report
                                </a>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- 2. Agenda / List View (Monthly Chronological List) --}}
        <div x-show="view === 'agenda'" class="bg-white rounded-2xl border border-neutral-200/90 shadow-xs overflow-hidden">
            <div class="p-5 sm:p-6 border-b border-neutral-200 flex items-center justify-between">
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-neutral-900">Agenda for {{ $monthLabel }}</h2>
                    <p class="text-xs text-neutral-500 mt-0.5">Chronological list of all blood collection drives in this month</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-neutral-100 text-neutral-700">
                    {{ count($agendaDrives) }} Drives Found
                </span>
            </div>

            @if($agendaDrives->isEmpty())
                <div class="p-12 text-center space-y-2">
                    <p class="text-sm font-semibold text-neutral-800">No drives scheduled for {{ $monthLabel }}</p>
                    <p class="text-xs text-neutral-500">Click "+ New Drive" to add an official drive to this month's calendar.</p>
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
                                        <x-badge :variant="$drive['status']->badgeVariant()" size="sm">
                                            {{ $drive['status']->label() }}
                                        </x-badge>
                                        @if($drive['is_live'])
                                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700">
                                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                                Active Today
                                            </span>
                                        @endif
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

                            {{-- Right Metrics & Actions --}}
                            <div class="flex flex-wrap items-center gap-4 self-start md:self-center shrink-0">
                                <div class="text-right">
                                    <span class="text-xs text-neutral-500 block">Booked / Capacity</span>
                                    <span class="text-sm font-bold text-neutral-900">
                                        {{ $drive['booked'] }} / {{ $drive['capacity'] }}
                                        <span class="text-xs font-semibold text-primary-700">({{ $drive['fill_rate'] }}%)</span>
                                    </span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <a
                                        href="{{ route('staff.drives.show', $drive['id']) }}"
                                        class="px-3.5 py-2 rounded-lg text-xs font-semibold bg-neutral-900 text-white hover:bg-neutral-800 transition-colors shadow-2xs"
                                    >
                                        Manage
                                    </a>
                                    <a
                                        href="{{ route('staff.drives.roster', $drive['id']) }}"
                                        class="px-3 py-2 rounded-lg text-xs font-semibold border border-neutral-300 text-neutral-700 bg-white hover:bg-neutral-50 transition-colors shadow-2xs"
                                    >
                                        Roster
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts.staff>
