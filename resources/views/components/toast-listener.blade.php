{{-- Instant, same-page toast for Livewire dispatch('toast', type: 'success'|'error', message: '...') calls. --}}
<div
    x-data="{ toasts: [] }"
    x-on:toast.window="
        const id = Date.now() + Math.random();
        toasts.push({ id, type: $event.detail.type ?? 'success', message: $event.detail.message });
        setTimeout(() => { toasts = toasts.filter(t => t.id !== id) }, 5000);
    "
    class="fixed top-4 inset-x-4 sm:inset-x-auto sm:right-4 z-50 flex flex-col items-stretch sm:items-end gap-2 pointer-events-none"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="true"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="w-full sm:w-96 bg-white rounded-lg shadow-lg border-l-4 overflow-hidden pointer-events-auto"
            :class="toast.type === 'success' ? 'border-primary-900' : 'border-red-500'"
        >
            <div class="flex items-start gap-3 px-4 py-3">
                <span class="flex-shrink-0 mt-0.5" :class="toast.type === 'success' ? 'text-primary-900' : 'text-red-500'">
                    <svg x-show="toast.type === 'success'" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l2.25 2.25 4.5-4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <svg x-show="toast.type !== 'success'" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </span>
                <p class="flex-1 text-sm text-gray-700" x-text="toast.message"></p>
                <button @click="toasts = toasts.filter(t => t.id !== toast.id)" type="button" class="flex-shrink-0 text-gray-400 hover:text-gray-600">
                    <span class="sr-only">Dismiss</span>
                    &times;
                </button>
            </div>
            <div
                class="h-0.5"
                :class="toast.type === 'success' ? 'bg-primary-900' : 'bg-red-500'"
                x-data="{ collapse: false }"
                x-init="setTimeout(() => collapse = true, 10)"
                :style="collapse ? 'width: 0%; transition: width 5000ms linear;' : 'width: 100%;'"
            ></div>
        </div>
    </template>
</div>
