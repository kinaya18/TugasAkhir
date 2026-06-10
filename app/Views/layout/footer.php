<script src="<?= base_url('assets/js/dashboard.js') ?>"></script>

<script>
function detectLocation() {

    const locationEl = document.getElementById('topbar-location');

    if (!locationEl) return;

    if (!navigator.geolocation) {
        locationEl.textContent = 'Lokasi tidak didukung';
        return;
    }

    navigator.geolocation.getCurrentPosition(

        async (position) => {

            try {

                const lat = position.coords.latitude;
                const lon = position.coords.longitude;

                window.currentLat = lat;
                window.currentLon = lon;

                const response = await fetch(
                    `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`
                );

                const data = await response.json();

                const addr = data.address || {};

                const location = [
                    addr.road,
                    addr.suburb,
                    addr.village,
                    addr.city || addr.town
                ]
                .filter(Boolean)
                .join(', ');

                locationEl.textContent = location;

                const linkEl = document.getElementById('topbar-location-link');

                if (linkEl) {
                    linkEl.href =
                        `https://www.google.com/maps?q=${lat},${lon}`;
                }

            } catch (err) {

                console.error(err);

                locationEl.textContent =
                    'Lokasi tidak diketahui';
            }
        },

        (error) => {

            console.error(error);

            locationEl.textContent =
                'Izin lokasi ditolak';
        }
    );
}

document.addEventListener('DOMContentLoaded', detectLocation);
</script>