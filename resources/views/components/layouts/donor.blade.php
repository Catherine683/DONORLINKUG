<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-neutral-100 text-neutral-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#B3262E">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    
    <title>{{ filled($title ?? null) ? $title . ' — Donor Portal — DonorLink UG' : 'Donor Portal — DonorLink UG' }}</title>
    <style>[x-cloak] { display: none !important; }</style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans antialiased bg-neutral-100 text-neutral-900 flex" x-data="{ sidebarOpen: false, collapsed: false }">
    {{-- Skip to content link for accessibility --}}
    <a href="#donor-main" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2.5 focus:bg-primary-600 focus:text-white focus:rounded-lg focus:shadow-lg focus:font-medium">
        Skip to main content
    </a>

    {{-- Mobile Drawer Backdrop --}}
    <div
        x-show="sidebarOpen"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
        class="fixed inset-0 z-40 bg-neutral-900/60 backdrop-blur-xs lg:hidden"
        x-on:click="sidebarOpen = false"
        aria-hidden="true"
    ></div>

    {{-- Sidebar (Desktop Collapsible & Mobile Drawer) --}}
    <aside
        :class="{
            'translate-x-0': sidebarOpen,
            '-translate-x-full lg:translate-x-0': !sidebarOpen,
            'w-64': !collapsed,
            'lg:w-20 w-64': collapsed
        }"
        class="fixed inset-y-0 left-0 z-50 flex flex-col bg-white border-r border-neutral-200 shadow-sm transition-all duration-200"
        aria-label="Donor navigation sidebar"
    >
        {{-- Sidebar Brand Header --}}
        <div class="h-16 px-4 flex items-center justify-between border-b border-neutral-200">
            <a href="{{ route('home') }}" class="flex items-center gap-2 overflow-hidden focus-visible:rounded-lg" aria-label="DonorLink UG Home">
                <x-logo size="sm" ::icon-only="collapsed" />
            </a>
            {{-- Mobile Close Button --}}
            <button
                type="button"
                x-on:click="sidebarOpen = false"
                class="lg:hidden p-1.5 rounded-lg text-neutral-500 hover:text-neutral-800 hover:bg-neutral-100 focus-visible:outline-primary-600 cursor-pointer min-h-[44px] min-w-[44px] flex items-center justify-center"
                aria-label="Close navigation"
            >
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Navigation Menu --}}
        <nav class="flex-1 px-3 py-4 space-y-1.5 overflow-y-auto" aria-label="Donor sections">
            {{-- Home / Dashboard --}}
            <a
                href="{{ route('donor.home') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('donor.home') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900' }}"
                :title="collapsed ? 'Home' : null"
            >
                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('donor.home') ? 'text-primary-600' : 'text-neutral-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span x-show="!collapsed" class="truncate">Home</span>
            </a>

            {{-- Find Drives --}}
            <a
                href="{{ route('drives.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('drives.*') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900' }}"
                :title="collapsed ? 'Find Drives' : null"
            >
                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('drives.*') ? 'text-primary-600' : 'text-neutral-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span x-show="!collapsed" class="truncate">Find Drives</span>
            </a>

            {{-- Calendar --}}
            <a
                href="{{ route('donor.calendar') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('donor.calendar*') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900' }}"
                :title="collapsed ? 'Calendar' : null"
            >
                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('donor.calendar*') ? 'text-primary-600' : 'text-neutral-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span x-show="!collapsed" class="truncate">Calendar</span>
            </a>

            {{-- Appointments --}}
            <a
                href="{{ route('donor.appointments') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('donor.appointments*') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900' }}"
                :title="collapsed ? 'Appointments' : null"
            >
                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('donor.appointments*') ? 'text-primary-600' : 'text-neutral-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                <span x-show="!collapsed" class="truncate">Appointments</span>
            </a>

            {{-- Donation History --}}
            <a
                href="{{ route('donor.history') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('donor.history') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900' }}"
                :title="collapsed ? 'History' : null"
            >
                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('donor.history') ? 'text-primary-600' : 'text-neutral-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span x-show="!collapsed" class="truncate">History</span>
            </a>

            {{-- Profile --}}
            <a
                href="{{ route('donor.profile') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('donor.profile') ? 'bg-primary-50 text-primary-700 font-semibold' : 'text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900' }}"
                :title="collapsed ? 'Profile' : null"
            >
                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('donor.profile') ? 'text-primary-600' : 'text-neutral-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span x-show="!collapsed" class="truncate">Profile</span>
            </a>
        </nav>

        {{-- Desktop Sidebar Collapse Toggle --}}
        <div class="hidden lg:block px-3 py-2 border-t border-neutral-200/80 shrink-0">
            <button
                type="button"
                x-on:click="collapsed = !collapsed"
                class="w-full flex items-center justify-center p-2 rounded-lg text-neutral-500 hover:bg-neutral-100 hover:text-neutral-800 transition-colors focus-visible:outline-primary-600 cursor-pointer min-h-[44px]"
                :title="collapsed ? 'Expand sidebar' : 'Collapse sidebar'"
                :aria-label="collapsed ? 'Expand sidebar' : 'Collapse sidebar'"
            >
                <svg x-show="!collapsed" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                </svg>
                <svg x-show="collapsed" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                </svg>
            </button>
        </div>

        {{-- User Footer Info --}}
        <div class="p-3 border-t border-neutral-200/80 bg-neutral-50/70 shrink-0">
            <div class="flex items-center gap-3 overflow-hidden">
                <span class="w-9 h-9 rounded-full bg-gradient-to-br from-primary-600 to-primary-700 text-white font-semibold text-xs flex items-center justify-center shrink-0 shadow-xs ring-1 ring-primary-200">
                    {{ auth()->user()?->initials() ?? 'D' }}
                </span>
                <div x-show="!collapsed" class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-neutral-900 truncate">{{ auth()->user()?->name ?? 'Donor' }}</p>
                    <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-semibold uppercase tracking-wider bg-primary-100 text-primary-800">
                        Donor
                    </span>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="mt-2" x-show="!collapsed">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2 px-2 py-1.5 rounded-lg text-xs font-medium text-danger-700 hover:bg-danger-50 transition-colors text-left cursor-pointer min-h-[44px]">
                    <svg class="w-4 h-4 text-danger-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Log Out
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Content Area --}}
    <div
        :class="{
            'lg:pl-64': !collapsed,
            'lg:pl-20': collapsed
        }"
        class="flex-1 flex flex-col min-w-0 transition-all duration-200"
    >
        {{-- Unified Top Header Bar --}}
        <header
            class="h-16 bg-white border-b border-neutral-200/90 px-4 sm:px-6 lg:px-8 flex items-center justify-between shrink-0 sticky top-0 z-30 shadow-xs"
            x-data="{ userDropdownOpen: false }"
        >
            {{-- Left Side: Mobile Menu Button & Portal Context --}}
            <div class="flex items-center gap-3">
                <button
                    type="button"
                    x-on:click="sidebarOpen = true"
                    class="lg:hidden p-2 -ml-2 rounded-xl text-neutral-600 hover:bg-neutral-100 focus-visible:outline-primary-600 cursor-pointer min-h-[44px] min-w-[44px] flex items-center justify-center"
                    aria-label="Open navigation menu"
                >
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                {{-- Mobile Logo --}}
                <a href="{{ route('home') }}" class="lg:hidden focus-visible:rounded-lg" aria-label="DonorLink UG Home">
                    <x-logo size="sm" />
                </a>

                {{-- Desktop Portal Context Tag --}}
                <div class="hidden lg:flex items-center gap-2.5 text-xs text-neutral-500">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-primary-50 text-primary-700 font-semibold border border-primary-200/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-primary-600 animate-pulse"></span>
                        Donor Portal
                    </span>
                    <span class="text-neutral-300">&bull;</span>
                    <span class="text-neutral-600 font-medium">
                        Uganda Voluntary Blood Donor Community
                    </span>
                </div>
            </div>

            {{-- Right Side: Quick Links, User Identity & Logout --}}
            <div class="flex items-center gap-2.5 sm:gap-3.5">
                {{-- Public Drives Link --}}
                <a
                    href="{{ route('drives.index') }}"
                    class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-neutral-700 bg-neutral-100 hover:bg-neutral-200 transition-colors"
                >
                    <svg class="w-3.5 h-3.5 text-neutral-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    </svg>
                    <span>Find Blood Drives</span>
                </a>

                {{-- User Dropdown --}}
                <div class="relative">
                    <button
                        type="button"
                        x-on:click="userDropdownOpen = !userDropdownOpen"
                        x-on:click.outside="userDropdownOpen = false"
                        class="flex items-center gap-2.5 p-1 rounded-xl hover:bg-neutral-100 transition-colors focus-visible:outline-primary-600 cursor-pointer min-h-[44px]"
                        aria-expanded="false"
                        :aria-expanded="userDropdownOpen.toString()"
                        aria-haspopup="true"
                        aria-label="User account menu"
                    >
                        <span class="w-8 h-8 rounded-full bg-primary-100 text-primary-800 font-semibold text-xs flex items-center justify-center ring-1 ring-primary-200">
                            {{ auth()->user()?->initials() ?? 'D' }}
                        </span>
                        <div class="hidden md:flex flex-col text-left">
                            <span class="text-xs font-bold text-neutral-900 leading-tight truncate max-w-[120px]">
                                {{ auth()->user()?->name ?? 'Donor' }}
                            </span>
                            <span class="text-[10px] text-neutral-500 font-medium">Voluntary Donor</span>
                        </div>
                        <svg class="w-4 h-4 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div
                        x-show="userDropdownOpen"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        x-cloak
                        class="absolute right-0 mt-2 w-56 rounded-xl bg-white border border-neutral-200 shadow-lg py-1 z-50 text-xs"
                    >
                        <div class="px-3.5 py-2.5 border-b border-neutral-100">
                            <p class="font-bold text-neutral-900 truncate">{{ auth()->user()?->name }}</p>
                            <p class="text-neutral-500 truncate text-[11px]">{{ auth()->user()?->email }}</p>
                        </div>
                        <a href="{{ route('donor.profile') }}" class="flex items-center gap-2 px-3.5 py-2 text-neutral-700 hover:bg-neutral-50 hover:text-neutral-900 font-medium">
                            <svg class="w-4 h-4 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            My Donor Profile
                        </a>
                        <a href="{{ route('donor.history') }}" class="flex items-center gap-2 px-3.5 py-2 text-neutral-700 hover:bg-neutral-50 hover:text-neutral-900 font-medium">
                            <svg class="w-4 h-4 text-neutral-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Donation History
                        </a>
                        <div class="border-t border-neutral-100 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-3.5 py-2 text-danger-700 hover:bg-danger-50 font-medium text-left cursor-pointer min-h-[44px]">
                                <svg class="w-4 h-4 text-danger-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Main Page Content --}}
        <main id="donor-main" class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto bg-neutral-100/70 focus:outline-hidden">
            <div class="max-w-7xl mx-auto space-y-6">
                {{ $slot }}
            </div>
        </main>
    </div>

    @fluxScripts
    @stack('scripts')
</body>
</html>
