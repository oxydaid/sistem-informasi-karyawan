<!-- Sidebar for Mobile (Slide-over) -->
<div x-show="mobileSidebarOpen" class="relative z-50 lg:hidden" x-ref="dialog" role="dialog" aria-modal="true" style="display: none;">
    <div x-show="mobileSidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="transition-opacity ease-linear duration-300" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm"></div>

    <div class="fixed inset-0 flex">
        <div x-show="mobileSidebarOpen" 
             x-transition:enter="transition ease-in-out duration-300 transform" 
             x-transition:enter-start="-translate-x-full" 
             x-transition:enter-end="translate-x-0" 
             x-transition:leave="transition ease-in-out duration-300 transform" 
             x-transition:leave-start="translate-x-0" 
             x-transition:leave-end="-translate-x-full" 
             class="relative mr-16 flex w-full max-w-xs flex-1 flex-col bg-white pt-5 pb-4"
             @click.away="mobileSidebarOpen = false">
            
            <div class="absolute top-0 right-0 -mr-12 pt-2">
                <button type="button" @click="mobileSidebarOpen = false" class="ml-1 flex h-10 w-10 items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                    <span class="sr-only">Close sidebar</span>
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Sidebar Brand Mobile -->
            <div class="flex flex-shrink-0 items-center px-6">
                @if($appSettings->logo_path)
                    <img class="h-8 w-auto" src="{{ asset('storage/' . $appSettings->logo_path) }}" alt="{{ $appSettings->app_name }}">
                @else
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary text-white shadow-md shadow-sky-500/20">
                        <span class="text-xl font-bold">ISP</span>
                    </div>
                    <span class="ml-3 text-lg font-bold tracking-wider text-slate-900">{{ $appSettings->app_name ?? 'ISP HRIS' }}</span>
                @endif
            </div>
            
            <!-- Navigation Mobile -->
            <div class="mt-5 h-0 flex-1 overflow-y-auto px-4">
                <nav class="space-y-1.5">
                    @include('layouts.partials.navigation')
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Static Sidebar for Desktop -->
<div class="hidden lg:fixed lg:inset-y-0 lg:z-40 lg:flex lg:w-72 lg:flex-col">
    <div class="flex min-h-0 flex-1 flex-col border-r border-slate-200/60 bg-white shadow-sm">
        <!-- Brand Header -->
        <div class="flex h-20 flex-shrink-0 items-center px-8 border-b border-slate-100">
            @if($appSettings->logo_path)
                <img class="h-10 w-auto" src="{{ asset('storage/' . $appSettings->logo_path) }}" alt="{{ $appSettings->app_name }}">
            @else
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-primary text-white shadow-lg shadow-sky-500/25">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <span class="ml-3.5 text-xl font-extrabold tracking-tight bg-gradient-to-r from-slate-900 via-slate-800 to-slate-700 bg-clip-text text-transparent">{{ $appSettings->app_name ?? 'ISP HRIS' }}</span>
            @endif
        </div>
        
        <!-- Navigation Menu -->
        <div class="flex flex-1 flex-col overflow-y-auto pt-6 pb-4 px-4">
            <nav class="flex-1 space-y-1.5">
                @include('layouts.partials.navigation')
            </nav>
            
            <!-- Footer Sidebar User Info -->
            @auth
            <div class="mt-auto border-t border-slate-100 pt-4 px-2">
                <div class="flex items-center">
                    <div class="h-10 w-10 flex-shrink-0 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold border border-slate-200">
                        {{ substr(auth()->user()->name, 0, 2) }}
                    </div>
                    <div class="ml-3 min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</p>
                        <p class="truncate text-xs font-medium text-slate-400 capitalize">{{ str_replace('_', ' ', auth()->user()->role) }}</p>
                    </div>
                </div>
            </div>
            @endauth
        </div>
    </div>
</div>
