{{-- Global confirm dialog. Trigger from anywhere with:
    x-on:click="$store.confirmDialog.open({
        title: 'Delete listing?',
        message: 'This cannot be undone.',
        variant: 'danger',
        confirmText: 'Delete',
        onConfirm: () => $wire.delete(123),
    })"
--}}
<div
    x-data
    x-show="$store.confirmDialog.show"
    x-cloak
    x-on:keydown.escape.window="$store.confirmDialog.close()"
    class="fixed inset-0 z-[70] overflow-y-auto"
    style="display: none;"
>
    <div
        x-show="$store.confirmDialog.show"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-primary-900/40 backdrop-blur-[2px]"
        x-on:click="$store.confirmDialog.close()"
    ></div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div
            x-show="$store.confirmDialog.show"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
            class="relative w-full max-w-sm bg-white rounded-xl shadow-2xl ring-1 ring-gray-900/10 p-6"
            @click.outside="$store.confirmDialog.close()"
        >
            <div class="flex items-start gap-4">
                <span
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg"
                    :class="$store.confirmDialog.variant === 'danger' ? 'bg-red-50 text-red-700' : 'bg-cream text-primary-900'"
                >
                    <svg x-show="$store.confirmDialog.variant === 'danger'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                    <svg x-show="$store.confirmDialog.variant !== 'danger'" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75l1.5 1.5 4.5-4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>

                <div class="flex-1 pt-1">
                    <h3 class="font-semibold text-base text-primary-900" x-text="$store.confirmDialog.title"></h3>
                    <p class="mt-1.5 text-sm text-gray-500" x-text="$store.confirmDialog.message"></p>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button
                    type="button"
                    @click="$store.confirmDialog.close()"
                    class="inline-flex items-center px-4 py-2.5 rounded-md border border-gray-300 bg-white text-sm font-medium leading-none text-primary-900 hover:bg-gray-50 transition-colors"
                >
                    <span x-text="$store.confirmDialog.cancelText"></span>
                </button>
                <button
                    type="button"
                    @click="$store.confirmDialog.confirm()"
                    class="inline-flex items-center px-4 py-2.5 rounded-md text-sm font-medium leading-none text-white transition-colors active:translate-y-px"
                    :class="$store.confirmDialog.variant === 'danger' ? 'bg-red-700 hover:bg-red-800' : 'bg-primary-900 hover:bg-primary-800'"
                >
                    <span x-text="$store.confirmDialog.confirmText"></span>
                </button>
            </div>
        </div>
    </div>
</div>
