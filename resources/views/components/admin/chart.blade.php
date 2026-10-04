@props(['type' => 'bar', 'labels' => [], 'datasets' => [], 'height' => '280px'])

@php
    $palette = ['#1e5850', '#C9A227', '#0F3D3E', '#6b8f89', '#8fb8b3', '#e2c766'];
    $isRadial = in_array($type, ['pie', 'doughnut', 'polarArea'], true);

    $preparedDatasets = collect($datasets)->values()->map(function ($dataset, $i) use ($palette, $isRadial) {
        $base = $isRadial
            ? ['backgroundColor' => $palette, 'borderColor' => '#ffffff', 'borderWidth' => 2]
            : ['backgroundColor' => $palette[$i % count($palette)], 'borderColor' => $palette[$i % count($palette)], 'borderRadius' => 6];

        return array_merge($base, $dataset);
    })->all();

    $scalesOption = $isRadial
        ? '{}'
        : "{ y: { beginAtZero: true, grid: { color: '#f3f4f6' } }, x: { grid: { display: false } } }";
@endphp

<div
    wire:ignore
    x-data="{
        init() {
            new Chart(this.$refs.canvas, {
                type: @js($type),
                data: { labels: @js($labels), datasets: @js($preparedDatasets) },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: {{ $isRadial || count($preparedDatasets) > 1 ? 'true' : 'false' }}, position: 'bottom', labels: { font: { family: 'Inter' } } } },
                    scales: {!! $scalesOption !!},
                },
            });
        }
    }"
    style="height: {{ $height }}"
>
    <canvas x-ref="canvas"></canvas>
</div>
