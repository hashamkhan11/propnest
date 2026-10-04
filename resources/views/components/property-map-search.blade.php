@props(['pins' => []])

<div
    wire:ignore
    x-data="{
        map: null,
        clusterGroup: null,
        markers: {},
        mode: 'none',
        showSearchThisArea: false,
        radiusCenter: null,
        radiusKm: 5,
        radiusMarker: null,
        radiusCircle: null,
        drawPoints: [],
        drawPolyline: null,
        drawPolygonLayer: null,
        lastPins: [],
        userMarker: null,
        locating: false,
        init() {
            this.$nextTick(() => {
                if (this.$refs.mapEl._leaflet_id) {
                    return;
                }

                this.map = L.map(this.$refs.mapEl).setView([20, 0], 2);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors',
                    maxZoom: 19,
                }).addTo(this.map);

                this.clusterGroup = L.markerClusterGroup();
                this.map.addLayer(this.clusterGroup);

                const initialPins = @js($pins);
                this.renderMarkers(initialPins);

                if (initialPins.length > 0) {
                    this.map.fitBounds(this.clusterGroup.getBounds(), { padding: [20, 20] });
                }

                this.map.on('moveend', () => {
                    if (this.mode === 'none') {
                        this.showSearchThisArea = true;
                    }
                });

                this.map.on('click', (e) => {
                    if (this.mode === 'radius') {
                        this.onMapClickForRadius(e);
                    } else if (this.mode === 'draw') {
                        this.onMapClickForDraw(e);
                    }
                });

                this.map.on('dblclick', () => {
                    if (this.mode === 'draw') {
                        this.closeDrawPolygon();
                    }
                });

                this.$watch('mode', (value) => {
                    if (value === 'draw') {
                        this.map.doubleClickZoom.disable();
                    } else {
                        this.map.doubleClickZoom.enable();
                    }
                });

                this.$watch(() => this.$store.mapSync.hoveredId, (id) => this.highlightMarker(id));

                document.addEventListener('livewire:navigating', () => {
                    this.map?.remove();
                }, { once: true });
            });
        },
        priceLabel(pin) {
            return pin.formattedPrice + (pin.purpose === 'for_rent' ? '/mo' : '');
        },
        createPriceIcon(pin) {
            return L.divIcon({
                className: 'price-marker',
                html: '<div class=\'price-marker-bubble\'>' + this.priceLabel(pin) + '</div>',
                iconSize: [0, 0],
                iconAnchor: [0, 0],
            });
        },
        locateMe() {
            if (!navigator.geolocation) {
                return;
            }

            this.locating = true;

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    this.locating = false;
                    this.$store.mapSync.userLat = position.coords.latitude;
                    this.$store.mapSync.userLng = position.coords.longitude;

                    if (this.userMarker) {
                        this.userMarker.remove();
                    }

                    this.userMarker = L.marker([this.$store.mapSync.userLat, this.$store.mapSync.userLng], {
                        icon: L.divIcon({
                            className: 'user-location-marker',
                            html: '<div class=\'user-location-dot\'></div>',
                            iconSize: [18, 18],
                            iconAnchor: [9, 9],
                        }),
                        zIndexOffset: 1000,
                    }).addTo(this.map);
                    this.userMarker.bindPopup('Your location');

                    this.renderMarkers(this.lastPins);
                },
                () => {
                    this.locating = false;
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        },
        renderMarkers(pins) {
            if (!this.clusterGroup) {
                return;
            }

            this.lastPins = pins;
            this.clusterGroup.clearLayers();
            this.markers = {};

            pins.forEach((pin) => {
                const marker = L.marker([pin.lat, pin.lng], { icon: this.createPriceIcon(pin) });

                const popupLink = document.createElement('a');
                popupLink.href = pin.url;
                popupLink.className = 'block';

                if (pin.thumbnail) {
                    const popupImg = document.createElement('img');
                    popupImg.src = pin.thumbnail;
                    popupImg.style.cssText = 'width:100%;height:80px;object-fit:cover;border-radius:4px;';
                    popupLink.appendChild(popupImg);
                }

                const popupPrice = document.createElement('div');
                popupPrice.style.cssText = 'margin-top:4px;font-weight:600;';
                popupPrice.textContent = this.priceLabel(pin);
                popupLink.appendChild(popupPrice);

                const popupTitle = document.createElement('div');
                popupTitle.style.fontSize = '12px';
                popupTitle.textContent = pin.title;
                popupLink.appendChild(popupTitle);

                const distance = this.$store.mapSync.distanceLabel(pin.lat, pin.lng);
                if (distance) {
                    const popupDistance = document.createElement('div');
                    popupDistance.style.cssText = 'font-size:11px;color:#6b7280;margin-top:2px;';
                    popupDistance.textContent = distance;
                    popupLink.appendChild(popupDistance);
                }

                marker.bindPopup(popupLink);

                marker.on('mouseover', () => { this.$store.mapSync.hoveredId = pin.id; });
                marker.on('mouseout', () => {
                    if (this.$store.mapSync.hoveredId === pin.id) {
                        this.$store.mapSync.hoveredId = null;
                    }
                });
                marker.on('click', () => {
                    this.$store.mapSync.hoveredId = pin.id;
                    this.$store.mapSync.selectedId = pin.id;
                });

                this.markers[pin.id] = marker;
                this.clusterGroup.addLayer(marker);
            });
        },
        highlightMarker(id) {
            Object.entries(this.markers).forEach(([pinId, marker]) => {
                const el = marker.getElement();
                if (!el) return;
                el.classList.toggle('map-pin-highlighted', Number(pinId) === id);
            });
        },
        activateRadiusTool() {
            this.mode = 'radius';
            this.clearDrawTool();
        },
        onMapClickForRadius(e) {
            const { lat, lng } = e.latlng;
            this.radiusCenter = { lat, lng };

            if (this.radiusMarker) this.radiusMarker.remove();
            if (this.radiusCircle) this.radiusCircle.remove();

            this.radiusMarker = L.marker([lat, lng]).addTo(this.map);
            this.radiusCircle = L.circle([lat, lng], { radius: this.radiusKm * 1000, color: '#2f726b', fillOpacity: 0.1 }).addTo(this.map);
        },
        updateRadiusCircle() {
            if (this.radiusCircle) {
                this.radiusCircle.setRadius(this.radiusKm * 1000);
            }
        },
        confirmRadiusSearch() {
            if (!this.radiusCenter) return;
            this.$wire.applyRadiusSearch({ lat: this.radiusCenter.lat, lng: this.radiusCenter.lng }, this.radiusKm);
            this.mode = 'none';
        },
        clearRadiusTool() {
            if (this.radiusMarker) { this.radiusMarker.remove(); this.radiusMarker = null; }
            if (this.radiusCircle) { this.radiusCircle.remove(); this.radiusCircle = null; }
            this.radiusCenter = null;
        },
        activateDrawTool() {
            this.mode = 'draw';
            this.clearRadiusTool();
            this.clearDrawTool();
        },
        onMapClickForDraw(e) {
            const { lat, lng } = e.latlng;

            if (this.drawPoints.length >= 3) {
                const first = this.drawPoints[0];
                if (this.map.distance([lat, lng], [first.lat, first.lng]) < 50) {
                    this.closeDrawPolygon();
                    return;
                }
            }

            this.drawPoints.push({ lat, lng });
            this.redrawDrawLine();
        },
        redrawDrawLine() {
            if (this.drawPolyline) this.drawPolyline.remove();
            this.drawPolyline = L.polyline(this.drawPoints.map((p) => [p.lat, p.lng]), { color: '#2f726b' }).addTo(this.map);
        },
        closeDrawPolygon() {
            if (this.drawPoints.length < 3) return;

            if (this.drawPolyline) { this.drawPolyline.remove(); this.drawPolyline = null; }

            this.drawPolygonLayer = L.polygon(this.drawPoints.map((p) => [p.lat, p.lng]), { color: '#2f726b', fillOpacity: 0.1 }).addTo(this.map);
            this.$wire.applyPolygonSearch(this.drawPoints.map((p) => [p.lat, p.lng]));
            this.mode = 'none';
        },
        clearDrawTool() {
            this.drawPoints = [];
            if (this.drawPolyline) { this.drawPolyline.remove(); this.drawPolyline = null; }
            if (this.drawPolygonLayer) { this.drawPolygonLayer.remove(); this.drawPolygonLayer = null; }
        },
        clearMapFilter() {
            this.mode = 'none';
            this.clearRadiusTool();
            this.clearDrawTool();
            this.$wire.clearGeoSearch();
        },
        searchThisArea() {
            const b = this.map.getBounds();
            this.$wire.searchThisArea({
                north: b.getNorth(),
                south: b.getSouth(),
                east: b.getEast(),
                west: b.getWest(),
            });
            this.showSearchThisArea = false;
        },
    }"
    x-on:property-pins-updated.window="renderMarkers($event.detail.pins)"
    class="relative"
>
    <div class="absolute z-[1000] top-3 left-3" x-cloak>
        <button
            type="button"
            @click="searchThisArea()"
            x-show="showSearchThisArea"
            class="bg-white shadow-md rounded-md px-3 py-1.5 text-sm font-medium text-primary-700 hover:bg-primary-50"
        >
            Search this area
        </button>
    </div>

    <div class="absolute z-[1000] top-3 right-3 flex flex-col gap-1 bg-white shadow-md rounded-md p-2">
        <button type="button" @click="locateMe()" :disabled="locating" :class="$store.mapSync.userLat !== null ? 'bg-primary-600 text-white' : 'text-gray-700 hover:bg-gray-100'" class="rounded px-2 py-1 text-xs font-medium disabled:opacity-50" x-text="locating ? 'Locating…' : 'My Location'"></button>
        <button type="button" @click="activateRadiusTool()" :class="mode === 'radius' ? 'bg-primary-600 text-white' : 'text-gray-700 hover:bg-gray-100'" class="rounded px-2 py-1 text-xs font-medium">Radius</button>
        <button type="button" @click="activateDrawTool()" :class="mode === 'draw' ? 'bg-primary-600 text-white' : 'text-gray-700 hover:bg-gray-100'" class="rounded px-2 py-1 text-xs font-medium">Draw</button>
        <button type="button" @click="clearMapFilter()" class="rounded px-2 py-1 text-xs font-medium text-gray-700 hover:bg-gray-100">Clear map filter</button>
    </div>

    <div x-show="mode === 'radius'" x-cloak class="absolute z-[1000] bottom-3 left-3 right-3 sm:right-auto bg-white shadow-md rounded-md p-3 w-auto sm:w-56">
        <label class="text-xs font-medium text-gray-700">Radius: <span x-text="radiusKm"></span> km</label>
        <input type="range" min="1" max="50" step="1" x-model.number="radiusKm" @input="updateRadiusCircle()" class="w-full">
        <button type="button" @click="confirmRadiusSearch()" :disabled="!radiusCenter" class="mt-2 w-full bg-primary-600 disabled:opacity-40 text-white text-xs font-medium rounded px-2 py-1.5">
            Search this radius
        </button>
        <p x-show="!radiusCenter" class="mt-1 text-[11px] text-gray-500">Click the map to place the center.</p>
    </div>

    <div x-show="mode === 'draw'" x-cloak class="absolute z-[1000] bottom-3 left-3 right-3 sm:right-auto bg-white shadow-md rounded-md p-3 w-auto sm:w-56">
        <p class="text-[11px] text-gray-500">Click to add points, click the first point (or double-click) to close the shape.</p>
        <button type="button" @click="clearDrawTool()" class="mt-2 w-full bg-white border border-gray-300 text-xs font-medium rounded px-2 py-1.5 hover:bg-gray-50">
            Clear drawing
        </button>
    </div>

    <div x-ref="mapEl" class="rounded-md w-full h-[380px] sm:h-[480px] lg:h-[600px]"></div>
</div>
