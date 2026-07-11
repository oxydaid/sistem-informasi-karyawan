<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? ($appSettings->app_name ?? config('app.name')) }}</title>

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Dynamic Theme Colors -->
        <style>
            :root {
                --color-primary: {{ $appSettings->primary_color ?? '#0ea5e9' }};
                --color-secondary: {{ $appSettings->secondary_color ?? '#334155' }};
                font-family: 'Outfit', sans-serif;
            }
        </style>

        <!-- Chart.js CDN -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles

        <!-- AlpineJS is included by Livewire -->
    </head>
    <body class="h-full text-slate-800 antialiased" x-data="{ mobileSidebarOpen: false }">
        
        <div class="min-h-full">
            <!-- Sidebar (Mobile & Desktop) -->
            <x-sidebar />

            <!-- Main Workspace Container -->
            <div class="flex flex-col lg:pl-72">
                <!-- Top Header Navbar -->
                <div class="sticky top-0 z-30 flex h-20 flex-shrink-0 bg-white/80 backdrop-blur-md border-b border-slate-200/50">
                    <button type="button" @click="mobileSidebarOpen = true" class="border-r border-slate-100 px-6 text-slate-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary lg:hidden">
                        <span class="sr-only">Open sidebar</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    
                    <div class="flex flex-1 justify-between px-6 lg:px-8">
                        <!-- Left Title/Breadcrumb -->
                        <div class="flex items-center">
                            <h1 class="text-xl font-bold tracking-tight text-slate-900">
                                {{ $title ?? 'Dashboard' }}
                            </h1>
                        </div>
                        
                        <!-- Right User Menu/Quick Actions -->
                        <div class="ml-4 flex items-center md:ml-6 space-x-4">
                            <!-- Quick Notifications or App Settings info -->
                            @auth
                            <div class="relative" x-data="{ open: false }">
                                <button type="button" @click="open = !open" class="flex max-w-xs items-center rounded-xl bg-slate-50 p-1.5 text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                                    @if(auth()->user()->employee && !empty(auth()->user()->employee->documents['pas_foto']) && \Storage::disk('public')->exists(auth()->user()->employee->documents['pas_foto']))
                                        <img class="h-7 w-7 rounded-full object-cover border border-slate-200" src="{{ asset('storage/' . auth()->user()->employee->documents['pas_foto']) }}" alt="">
                                    @else
                                        <div class="h-7 w-7 rounded-full bg-slate-100 flex items-center justify-center font-bold text-slate-600 border border-slate-200 text-xs">
                                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <span class="hidden md:block ml-2 text-sm font-medium text-slate-700">{{ auth()->user()->name }}</span>
                                    <svg class="hidden md:block ml-1 h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                
                                <div x-show="open" 
                                     @click.away="open = false" 
                                     x-transition:enter="transition ease-out duration-100" 
                                     x-transition:enter-start="transform opacity-0 scale-95" 
                                     x-transition:enter-end="transform opacity-100 scale-100" 
                                     x-transition:leave="transition ease-in duration-75" 
                                     x-transition:leave-start="transform opacity-100 scale-100" 
                                     x-transition:leave-end="transform opacity-0 scale-95" 
                                     class="absolute right-0 z-50 mt-2 w-48 origin-top-right rounded-2xl bg-white py-1 shadow-lg ring-1 ring-black/5 focus:outline-none" 
                                     role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1" style="display: none;">
                                    
                                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                                        @csrf
                                        <button type="submit" class="flex w-full items-center px-4 py-2.5 text-sm text-rose-600 hover:bg-rose-50/50 rounded-xl transition" role="menuitem">
                                            <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            Sign Out
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- Main Content Slot -->
                <main class="flex-1 py-10">
                    <div class="mx-auto max-w-7xl px-6 lg:px-8">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>

        <x-toast />

        @livewireScripts
    </body>
</html>
