@props(['type' => 'bar', 'labels' => [], 'datasets' => [], 'height' => '280px', 'horizontal' => false, 'money' => false])

@php
    // Single-series charts only: one ink hue, so color never has to carry identity.
    $ink = '#C95B2C';
    $isLine = $type === 'line';

    $preparedDatasets = collect($datasets)->values()->map(function ($dataset) use ($ink, $isLine) {
        $base = $isLine
            ? ['borderColor' => $ink, 'backgroundColor' => 'rgba(201, 91, 44, 0.08)', 'borderWidth' => 2, 'pointRadius' => 0, 'pointHoverRadius' => 4, 'pointHoverBackgroundColor' => $ink, 'pointHoverBorderColor' => '#ffffff', 'pointHoverBorderWidth' => 2, 'tension' => 0.3, 'cubicInterpolationMode' => 'monotone']
            : ['backgroundColor' => $ink, 'hoverBackgroundColor' => '#AC4720', 'borderRadius' => 4, 'borderSkipped' => 'start', 'maxBarThickness' => 22, 'categoryPercentage' => 0.7];

        return array_merge($base, $dataset);
    })->all();

    $valueAxis = $horizontal ? 'x' : 'y';
    $categoryAxis = $horizontal ? 'y' : 'x';
@endphp

<div
    wire:ignore
    x-data="{
        init() {
            const money = @js($money);
            const fmt = (v) => money ? '$' + Number(v).toLocaleString() : Number(v).toLocaleString();
            const font = { family: 'Geist', size: 12 };
            new Chart(this.$refs.canvas, {
                type: @js($type),
                data: { labels: @js($labels), datasets: @js($preparedDatasets) },
                options: {
                    indexAxis: @js($horizontal ? 'y' : 'x'),
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: navigator.webdriver ? false : { duration: 500 },
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#141311', padding: 10, cornerRadius: 6, displayColors: false,
                            titleFont: font, bodyFont: { ...font, weight: '600' },
                            callbacks: { label: (c) => fmt(c.parsed[@js($valueAxis)]) },
                        },
                    },
                    scales: {
                        {{ $valueAxis }}: { beginAtZero: true, grid: { color: '#EEECE8' }, border: { display: false }, ticks: { color: '#78716C', font, maxTicksLimit: 5, precision: 0, callback: (v) => fmt(v) } },
                        {{ $categoryAxis }}: { grid: { display: false }, border: { color: '#E7E5E4' }, ticks: { color: '#57534E', font, autoSkip: true, maxRotation: 0 } },
                    },
                },
            });
        }
    }"
    style="height: {{ $height }}"
    {{ $attributes }}
>
    <canvas x-ref="canvas" role="img" aria-label="{{ $attributes->get('aria-label', 'Chart') }}"></canvas>
</div>
