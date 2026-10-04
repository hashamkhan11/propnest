@props([
    'latitude' => null,
    'longitude' => null,
    'readonly' => false,
    'latitudeInputId' => 'latitude',
    'longitudeInputId' => 'longitude',
])

@php
    $defaultLat = 40.7128;
    $defaultLng = -74.0060;
@endphp

<div
    wire:ignore
    x-data="{
        map: null,
        marker: null,
        readonly: @js($readonly),
        latInputId: @js($latitudeInputId),
        lngInputId: @js($longitudeInputId),
        init() {
            if (this.$refs.mapEl._leaflet_id) {
                return;
            }

            const rawLat = @js($latitude);
            const rawLng = @js($longitude);
            const startLat = rawLat !== null ? parseFloat(rawLat) : {{ $defaultLat }};
            const startLng = rawLng !== null ? parseFloat(rawLng) : {{ $defaultLng }};

            this.map = L.map(this.$refs.mapEl).setView([startLat, startLng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 19,
            }).addTo(this.map);

            this.marker = L.marker([startLat, startLng], { draggable: !this.readonly }).addTo(this.map);

            document.addEventListener('livewire:navigating', () => {
                this.map?.remove();
            }, { once: true });

            if (this.readonly) {
                return;
            }

            this.marker.on('dragend', () => {
                const pos = this.marker.getLatLng().wrap();
                this.marker.setLatLng(pos);
                this.setInputs(pos.lat, pos.lng);
            });

            this.map.on('click', (e) => {
                const pos = e.latlng.wrap();
                this.marker.setLatLng(pos);
                this.setInputs(pos.lat, pos.lng);
            });

            const latInput = document.getElementById(this.latInputId);
            const lngInput = document.getElementById(this.lngInputId);

            [latInput, lngInput].forEach((el) => {
                el.addEventListener('input', () => {
                    const lat = parseFloat(latInput.value);
                    const lng = parseFloat(lngInput.value);

                    if (!isNaN(lat) && !isNaN(lng)) {
                        this.marker.setLatLng([lat, lng]);
                        this.map.panTo([lat, lng]);
                    }
                });
            });
        },
        setInputs(lat, lng) {
            const latInput = document.getElementById(this.latInputId);
            const lngInput = document.getElementById(this.lngInputId);

            latInput.value = lat.toFixed(7);
            lngInput.value = lng.toFixed(7);

            latInput.dispatchEvent(new Event('input', { bubbles: true }));
            lngInput.dispatchEvent(new Event('input', { bubbles: true }));
        },
    }"
>
    <div x-ref="mapEl" class="rounded-md w-full h-64 sm:h-80"></div>
</div>
