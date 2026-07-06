<div>
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <!-- Logo / Brand -->
        <div class="flex justify-center">
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-primary text-white shadow-xl shadow-sky-500/30">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
        </div>
        <h2 class="mt-6 text-center text-3xl font-extrabold tracking-tight text-slate-900">
            Sign in to {{ $appSettings->app_name ?? 'ISP HRIS' }}
        </h2>
        <p class="mt-2 text-center text-sm text-slate-500">
            Or
            <a href="{{ route('apply') }}" class="font-semibold text-primary hover:text-primary/80 transition">
                apply to join as a candidate
            </a>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow-xl shadow-slate-200/50 sm:rounded-3xl sm:px-10 border border-slate-100">
            <form wire:submit.prevent="login" class="space-y-6">
                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700">Email Address</label>
                    <div class="mt-1.5">
                        <input wire:model="email" id="email" name="email" type="email" autocomplete="email" required 
                               class="block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-slate-950 placeholder-slate-400 shadow-sm focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-sm text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700">Password</label>
                    <div class="mt-1.5">
                        <input wire:model="password" id="password" name="password" type="password" autocomplete="current-password" required 
                               class="block w-full rounded-2xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-slate-950 placeholder-slate-400 shadow-sm focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 transition text-sm">
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-sm text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input wire:model="remember" id="remember-me" name="remember-me" type="checkbox" 
                               class="h-4.5 w-4.5 rounded border-slate-300 text-primary focus:ring-primary">
                        <label for="remember-me" class="ml-2.5 block text-sm font-medium text-slate-600">Remember me</label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="font-semibold text-primary hover:text-primary/80 transition">Forgot your password?</a>
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" wire:loading.attr="disabled"
                            class="flex w-full justify-center rounded-2xl bg-primary px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-sky-500/25 hover:shadow-xl hover:shadow-sky-500/30 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition active:scale-[0.98] disabled:opacity-75 disabled:cursor-not-allowed">
                        <!-- Loading indicator -->
                        <svg wire:loading class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove>Sign In</span>
                        <span wire:loading>Signing in...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
