@props(['headers' => []])

<div class="hidden md:block bg-white rounded-xl border border-gray-900/10 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-gray-900/10 font-mono text-[11px] uppercase tracking-[0.12em] text-gray-500 bg-cream/60">
                    @foreach ($headers as $header)
                        <th class="py-3 px-4 font-medium {{ $loop->last ? 'text-right' : '' }}">{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>
