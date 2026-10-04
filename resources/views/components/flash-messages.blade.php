@php
    $toasts = collect([
        session('success') ? ['type' => 'success', 'message' => session('success')] : null,
        session('status') ? ['type' => 'success', 'message' => session('status')] : null,
        session('error') ? ['type' => 'error', 'message' => session('error')] : null,
    ])->filter()->values();
@endphp

@if ($toasts->isNotEmpty())
    <div class="fixed top-4 inset-x-4 sm:inset-x-auto sm:right-4 z-50 flex flex-col items-stretch sm:items-end gap-2">
        @foreach ($toasts as $toast)
            <div
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 5000)"
                x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                data-toast-type="{{ $toast['type'] }}"
                class="w-full sm:w-96 bg-white rounded-lg shadow-lg border-l-4 {{ $toast['type'] === 'success' ? 'border-primary-600' : 'border-red-500' }} overflow-hidden"
            >
                <div class="flex items-start gap-3 px-4 py-3">
                    <span class="flex-shrink-0 mt-0.5 {{ $toast['type'] === 'success' ? 'text-primary-600' : 'text-red-500' }}">
                        @if ($toast['type'] === 'success')
                            <x-icon.check-circle class="w-5 h-5" />
                        @else
                            <x-icon.alert-circle class="w-5 h-5" />
                        @endif
                    </span>
                    <p class="flex-1 text-sm text-gray-700">{{ $toast['message'] }}</p>
                    <button @click="show = false" type="button" class="flex-shrink-0 text-gray-400 hover:text-gray-600">
                        <span class="sr-only">Dismiss</span>
                        &times;
                    </button>
                </div>
                <div
                    class="h-0.5 {{ $toast['type'] === 'success' ? 'bg-primary-600' : 'bg-red-500' }}"
                    x-data="{ collapse: false }"
                    x-init="setTimeout(() => collapse = true, 10)"
                    :style="collapse ? 'width: 0%; transition: width 5000ms linear;' : 'width: 100%;'"
                ></div>
            </div>
        @endforeach
    </div>
@endif
