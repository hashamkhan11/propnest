import Chart from 'chart.js/auto';
import L from 'leaflet';

window.Chart = Chart;
import 'leaflet.markercluster';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({ iconRetinaUrl: markerIcon2x, iconUrl: markerIcon, shadowUrl: markerShadow });

window.L = L;

document.addEventListener('alpine:init', () => {
    Alpine.store('mapSync', {
        hoveredId: null,
        selectedId: null,
        userLat: null,
        userLng: null,

        toRad(deg) {
            return (deg * Math.PI) / 180;
        },

        distanceKm(lat1, lng1, lat2, lng2) {
            const earthRadiusKm = 6371;
            const dLat = this.toRad(lat2 - lat1);
            const dLng = this.toRad(lng2 - lng1);
            const a = Math.sin(dLat / 2) ** 2
                + Math.cos(this.toRad(lat1)) * Math.cos(this.toRad(lat2)) * Math.sin(dLng / 2) ** 2;
            return earthRadiusKm * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        },

        distanceLabel(lat, lng) {
            if (this.userLat === null || this.userLng === null || lat === null || lng === null) {
                return null;
            }

            return this.distanceKm(this.userLat, this.userLng, lat, lng).toFixed(1) + ' km away';
        },
    });

    Alpine.store('confirmDialog', {
        show: false,
        title: '',
        message: '',
        confirmText: 'Confirm',
        cancelText: 'Cancel',
        variant: 'primary',
        onConfirm: null,

        open({ title = 'Are you sure?', message = '', confirmText = 'Confirm', cancelText = 'Cancel', variant = 'primary', onConfirm = null } = {}) {
            this.title = title;
            this.message = message;
            this.confirmText = confirmText;
            this.cancelText = cancelText;
            this.variant = variant;
            this.onConfirm = onConfirm;
            this.show = true;
        },

        confirm() {
            const action = this.onConfirm;
            this.show = false;
            if (typeof action === 'function') {
                action();
            }
        },

        close() {
            this.show = false;
        },
    });
});
