@props(['headers' => []])

<div class="hidden md:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-gray-100 text-xs uppercase tracking-wider text-gray-500 bg-gray-50/60">
                    @foreach ($headers as $header)
                        <th class="py-3 px-4 font-semibold {{ $loop->last ? 'text-right' : '' }}">{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>
