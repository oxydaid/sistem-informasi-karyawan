<div x-data="{
    toasts: [],
    add(detail) {
        // Prevent duplicate toasts in a short timeframe
        const isDuplicate = this.toasts.some(t => t.message === detail.message && (Date.now() - t.id < 500));
        if (isDuplicate) return;

        const id = Date.now();
        this.toasts.push({
            id: id,
            type: detail.type || 'info',
            title: detail.title || '',
            message: detail.message || '',
            show: true
        });
        setTimeout(() => {
            this.remove(id);
        }, 5000);
    },
    remove(id) {
        const toast = this.toasts.find(t => t.id === id);
        if (toast) {
            toast.show = false;
            setTimeout(() => {
                this.toasts = this.toasts.filter(t => t.id !== id);
            }, 300);
        }
    }
}"
@toast.window="add($event.detail)"
x-init="
    @if(session()->has('success'))
        add({ type: 'success', message: '{{ addslashes(session('success')) }}' });
    @endif
    @if(session()->has('error'))
        add({ type: 'error', message: '{{ addslashes(session('error')) }}' });
    @endif
    @if($errors->any())
        @foreach($errors->all() as $error)
            add({ type: 'error', title: 'Validasi Gagal', message: '{{ addslashes($error) }}' });
        @endforeach
    @endif
"
class="fixed top-6 right-6 z-[100] flex flex-col gap-3 w-full max-w-sm pointer-events-none">
    
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.show"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-y-2 opacity-0 scale-95"
             x-transition:enter-end="translate-y-0 opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="pointer-events-auto flex w-full items-start gap-3 rounded-2xl bg-white border p-4 shadow-xl"
             :class="{
                 'border-emerald-100 bg-emerald-50/80 backdrop-blur-sm text-emerald-900': toast.type === 'success',
                 'border-rose-100 bg-rose-50/80 backdrop-blur-sm text-rose-900': toast.type === 'error' || toast.type === 'danger',
                 'border-amber-100 bg-amber-50/80 backdrop-blur-sm text-amber-900': toast.type === 'warning',
                 'border-sky-100 bg-sky-50/80 backdrop-blur-sm text-sky-900': toast.type === 'info'
             }">
            
            <!-- Icon -->
            <div class="flex-shrink-0 mt-0.5">
                <template x-if="toast.type === 'success'">
                    <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </template>
                <template x-if="toast.type === 'error' || toast.type === 'danger'">
                    <svg class="h-5 w-5 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </template>
                <template x-if="toast.type === 'warning'">
                    <svg class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </template>
                <template x-if="toast.type === 'info'">
                    <svg class="h-5 w-5 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </template>
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0">
                <template x-if="toast.title">
                    <p class="text-xs font-bold text-slate-900" x-text="toast.title"></p>
                </template>
                <p class="text-xs font-semibold text-slate-800" :class="{ 'mt-0.5': toast.title }" x-text="toast.message"></p>
            </div>

            <!-- Close -->
            <div class="flex-shrink-0">
                <button type="button" @click="remove(toast.id)" class="text-slate-400 hover:text-slate-600 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </template>
</div>
