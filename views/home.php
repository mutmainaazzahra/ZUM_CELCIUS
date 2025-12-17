<?php include 'layout/header.php'; ?>

<?php
$isDefaultJakarta = false;
if (isset($currentWeather['coord'])) {
    $cLat = $currentWeather['coord']['lat'];
    $cLon = $currentWeather['coord']['lon'];
    if (abs($cLat - (-6.2088)) < 0.001 && abs($cLon - 106.8456) < 0.001) {
        $isDefaultJakarta = true;
    }
}
?>

<?php if (isset($error)): ?>
    <div class="container mt-3">
        <div class="alert alert-danger glass-card alert-dismissible fade show text-center border-0 mb-4 text-white" style="background: #ff5f5f;" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Lokasi Tidak Ditemukan!</strong> <?php echo $error; ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
<?php endif; ?>

<link href="https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.css" rel="stylesheet">
<style>
    .mapboxgl-ctrl-logo {
        display: none !important;
    }

    @keyframes pulse-text {
        0% {
            opacity: 0.6;
        }

        50% {
            opacity: 1;
        }

        100% {
            opacity: 0.6;
        }
    }

    .detecting-location {
        animation: pulse-text 1.5s infinite;
        color: #ffc107;
    }

    .bento-grid {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 1.5rem;
        padding: 2rem 0;
    }

    .col-span-12 {
        grid-column: span 12;
    }

    .col-span-8 {
        grid-column: span 8;
    }

    .col-span-4 {
        grid-column: span 4;
    }

    @media (max-width: 992px) {

        .col-span-8,
        .col-span-4 {
            grid-column: span 12;
        }
    }

    .temp-large {
        font-size: 5rem;
        font-weight: 800;
        line-height: 1;
        color: var(--headline);
    }

    .temp-unit {
        font-size: 2rem;
        font-weight: 600;
        vertical-align: top;
        color: var(--button);
    }

    .detail-item {
        text-align: center;
        padding: 1rem;
    }

    .detail-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--headline);
    }

    .detail-label {
        font-size: 0.9rem;
        opacity: 0.8;
        color: var(--paragraph);
    }

    /* Flip Card */
    .forecast-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .flip-card {
        background-color: transparent;
        perspective: 1000px;
        height: 180px;
        cursor: pointer;
    }

    .flip-card-inner {
        position: relative;
        width: 100%;
        height: 100%;
        text-align: center;
        transition: transform 0.8s cubic-bezier(0.4, 0.0, 0.2, 1);
        transform-style: preserve-3d;
    }

    .flip-card:hover .flip-card-inner {
        transform: rotateY(180deg);
    }

    .flip-card-front,
    .flip-card-back {
        position: absolute;
        width: 100%;
        height: 100%;
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
        border-radius: 20px;
        padding: 1.5rem;
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        border: var(--glass-border);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        box-shadow: var(--glass-shadow);
    }

    .flip-card-front h5 {
        font-size: 1rem;
        margin-bottom: 10px;
        color: var(--headline);
    }

    .flip-card-front img {
        width: 50px;
        margin-bottom: 5px;
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
    }

    .flip-card-front .forecast-temp {
        font-size: 1.5rem;
        font-weight: bold;
        color: var(--headline);
    }

    .flip-card-back {
        background: var(--headline);
        color: #fff;
        transform: rotateY(180deg);
    }

    /* Modal */
    .modal-header {
        border-bottom: 1px solid var(--tertiary);
    }

    .modal-footer {
        border-top: 1px solid var(--tertiary);
    }

    .btn-close-white {
        filter: none;
    }
</style>

<div class="container pb-5">
    <?php if (isset($forecast['city']) && $forecast !== null): ?>
        <div class="bento-grid">
            <div class="col-span-8">
                <div class="glass-card h-100 d-flex flex-column justify-content-between p-5 position-relative overflow-hidden">
                    <div>
                        <h1 class="fw-bold mb-0 text-shadow-sm" style="color: var(--headline);">
                            <i class="bi bi-geo-alt-fill text-warning me-2"></i> <?php echo $forecast['city']['name']; ?>
                        </h1>
                        <p class="fs-5 opacity-75" style="color: var(--paragraph);"><?php echo date("l, j F Y"); ?></p>
                    </div>
                    <div class="d-flex align-items-center mt-3">
                        <img src="https://openweathermap.org/img/wn/<?php echo $currentWeather['weather'][0]['icon']; ?>@4x.png" width="150" style="filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));">
                        <div class="ms-2">
                            <div class="d-flex align-items-start">
                                <span class="temp-large text-shadow"><?php echo round($currentWeather['main']['temp']); ?></span>
                                <span class="temp-unit">°C</span>
                            </div>
                            <h4 class="fw-light" style="color: var(--paragraph);"><?php echo ucfirst($currentWeather['weather'][0]['description']); ?></h4>
                            <p class="mb-0 opacity-90 mt-2" style="color: var(--paragraph);">
                                <i class="bi bi-thermometer-half me-1"></i>
                                Terasa seperti <strong><?php echo round($currentWeather['main']['feels_like']); ?>°C</strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-span-4">
                <div class="glass-card h-100 p-4">
                    <h5 class="fw-bold mb-4 text-warning"><i class="bi bi-grid-1x2-fill me-2"></i>Detail Cuaca</h5>
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-center p-3 rounded-3" style="background: var(--secondary);">
                            <i class="bi bi-droplet-fill fs-2 text-info me-3"></i>
                            <div>
                                <div class="small opacity-75 detail-label">Kelembapan</div>
                                <div class="fs-4 fw-bold detail-value"><?php echo $currentWeather['main']['humidity']; ?>%</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center p-3 rounded-3" style="background: var(--secondary);">
                            <i class="bi bi-wind fs-2 text-success me-3"></i>
                            <div>
                                <div class="small opacity-75 detail-label">Angin</div>
                                <div class="fs-4 fw-bold detail-value"><?php echo $currentWeather['wind']['speed']; ?> km/j</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center p-3 rounded-3" style="background: var(--secondary);">
                            <i class="bi bi-speedometer2 fs-2 text-warning me-3"></i>
                            <div>
                                <div class="small opacity-75 detail-label">Tekanan</div>
                                <div class="fs-4 fw-bold detail-value"><?php echo $currentWeather['main']['pressure']; ?> hPa</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- REKOMENDASI AKTIVITAS -->
            <div class="col-span-12">
                <div class="glass-card p-4 d-flex align-items-center shadow-sm"
                    style="border-left: 5px solid var(--bs-<?php echo $recommendation['color']; ?>); background: linear-gradient(90deg, <?php echo $recommendation['bg']; ?> 0%, #ffffff 100%);">

                    <div class="rounded-circle p-3 d-flex align-items-center justify-content-center me-3 shadow-sm"
                        style="width: 60px; height: 60px; background-color: white;">
                        <i class="bi <?php echo $recommendation['icon']; ?> fs-2 text-<?php echo $recommendation['color']; ?>"></i>
                    </div>

                    <div>
                        <h6 class="fw-bold text-uppercase mb-1 text-<?php echo $recommendation['color']; ?>" style="letter-spacing: 1px;">
                            Rekomendasi: <?php echo $recommendation['type']; ?>
                        </h6>
                        <p class="mb-0 text-dark fw-medium" style="opacity: 0.9;">
                            <?php echo $recommendation['message']; ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-span-12 mt-3">
                <h4 class="fw-bold mb-3" style="color: var(--headline);"><i class="bi bi-clock-history me-2"></i>Prediksi 5 Hari</h4>
                <div class="forecast-container">
                    <?php
                    $dailyForecasts = [];
                    foreach ($forecast['list'] as $item) {
                        if (strpos($item['dt_txt'], '12:00:00') !== false) {
                            $dailyForecasts[] = $item;
                        }
                    }
                    if (count($dailyForecasts) < 5) {
                        $dailyForecasts = [];
                        for ($i = 0; $i < count($forecast['list']); $i += 8) {
                            $dailyForecasts[] = $forecast['list'][$i];
                        }
                    }
                    $maxCards = min(count($dailyForecasts), 5);
                    for ($i = 0; $i < $maxCards; $i++):
                        $item = $dailyForecasts[$i];
                        $timeLabel = date('D, d M', strtotime($item['dt_txt']));
                        $tempValue = round($item['main']['temp']);
                        $iconPlaceholder = $item['weather'][0]['icon'];
                        $descPlaceholder = $item['weather'][0]['description'];
                        $feelsLikeForecast = round($item['main']['feels_like']);
                    ?>
                        <div class="flip-card">
                            <div class="flip-card-inner">
                                <div class="flip-card-front">
                                    <h5><?php echo $timeLabel; ?></h5>
                                    <img src="https://openweathermap.org/img/wn/<?php echo $iconPlaceholder; ?>.png" alt="Icon">
                                    <div class="fs-3 fw-bold"><?php echo $tempValue; ?>°C</div>
                                </div>
                                <div class="flip-card-back">
                                    <div>
                                        <p class="mb-1 fw-bold text-warning">Detail:</p>
                                        <p class="mb-0 text-capitalize"><?php echo $descPlaceholder; ?></p>
                                        <small class="opacity-75 mt-1 d-block">Terasa: <?php echo $feelsLikeForecast; ?>°C</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <div class="col-span-12">
                <div class="glass-card p-4">
                    <h5 class="fw-bold mb-4" style="color: var(--headline);"><i class="bi bi-graph-up me-2"></i>Grafik Tren Suhu</h5>
                    <div style="height: 320px;">
                        <canvas id="weatherChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <div class="glass-card text-center py-5 mx-auto" style="max-width: 600px; margin-top: 10vh;">
            <div id="loading-state">
                <div class="spinner-border text-warning mb-4" style="width: 3rem; height: 3rem;" role="status"></div>
                <h3 class="fw-bold text-shadow-md detecting-location" style="color: var(--headline);">Mendeteksi Lokasi Anda...</h3>
                <p class="lead opacity-75 mb-4" style="color: var(--paragraph);">Mohon izinkan akses lokasi di browser Anda.</p>
            </div>
            <div id="manual-state" style="display: none;">
                <i class="bi bi-geo-alt-fill display-1 text-danger mb-4"></i>
                <h3 class="fw-bold text-shadow-md" style="color: var(--headline);">Lokasi Tidak Terdeteksi</h3>
                <p class="lead opacity-75 mb-4" style="color: var(--paragraph);">GPS dimatikan atau izin ditolak. Silakan cari lokasi manual.</p>
                <button type="button" class="btn btn-warning rounded-pill mt-3 px-4" data-bs-toggle="modal" data-bs-target="#mapModal">Cari Manual</button>
            </div>
        </div>
    <?php endif; ?>

</div>
</div>

<div class="modal fade" id="mapModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content overflow-hidden p-0">
            <div class="modal-header border-bottom-0 p-4" style="background: rgba(0,0,0,0.2)">
                <h5 class="modal-title text-white fw-bold"><i class="bi bi-geo-alt me-2"></i> Pilih Lokasi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 position-relative">
                <div class="position-absolute top-0 start-0 end-0 p-3" style="z-index: 10;">
                    <div class="glass-card p-2 d-flex gap-2" style="border-radius: 50px;">
                        <input id="mapbox-search-input" class="form-control px-3" type="text" placeholder="Cari kota..." style="border-radius: 50px; background: rgba(255,255,255,0.2); color:white;">
                        <button type="button" id="btn-search-mapbox" class="btn btn-warning rounded-circle p-2" style="width: 42px; height: 42px;"><i class="bi bi-search"></i></button>
                        <button type="button" id="btn-current-loc" class="btn btn-primary rounded-circle p-2" style="width: 42px; height: 42px;"><i class="bi bi-crosshair"></i></button>
                    </div>
                </div>
                <div id="mapboxMap" style="width: 100%; height: 500px;"></div>
                <div class="position-absolute bottom-0 start-0 end-0 p-3" style="z-index: 10; background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);">
                    <div class="text-center text-white text-shadow-sm">
                        <span class="badge bg-warning text-dark me-2">Terpilih:</span>
                        <strong id="selected-loc-name" class="fs-5">Belum ada lokasi dipilih</strong>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 p-3" style="background: rgba(0,0,0,0.4); justify-content: center;">
                <button type="button" class="btn btn-outline-light rounded-pill me-2" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btn-select-location" class="btn btn-warning rounded-pill px-5 fw-bold"><i class="bi bi-check-lg me-2"></i> Gunakan Lokasi Ini</button>
            </div>
        </div>
    </div>
</div>

<script src="https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.js"></script>

<script>
    mapboxgl.accessToken = '<?php echo $_ENV['MAPBOX_ACCESS_TOKEN'] ?? ''; ?>';
    let map, marker;
    let selectedLat, selectedLon, selectedName;
    const mapModal = document.getElementById('mapModal');

    mapModal.addEventListener('shown.bs.modal', function() {
        if (!map) {
            initMapbox();
        } else {
            setTimeout(() => {
                map.resize();
            }, 200);
        }
    });

    function initMapbox() {
        const startLng = <?php echo $lon ?? 106.8456; ?>;
        const startLat = <?php echo $lat ?? -6.2088; ?>;
        map = new mapboxgl.Map({
            container: 'mapboxMap',
            style: 'mapbox://styles/mapbox/streets-v12',
            center: [startLng, startLat],
            zoom: 13
        });
        map.addControl(new mapboxgl.NavigationControl());
        marker = new mapboxgl.Marker({
            draggable: true,
            color: "#ffc107"
        }).setLngLat([startLng, startLat]).addTo(map);
        marker.on('dragend', () => {
            const ll = marker.getLngLat();
            updateLocationInfo(ll.lng, ll.lat);
        });
        map.on('click', (e) => {
            marker.setLngLat(e.lngLat);
            updateLocationInfo(e.lngLat.lng, e.lngLat.lat);
        });
        <?php if (isset($_SESSION['last_city_name'])): ?>
            selectedName = "<?php echo htmlspecialchars($_SESSION['last_city_name']); ?>";
            document.getElementById('selected-loc-name').innerText = selectedName;
            selectedLat = startLat;
            selectedLon = startLng;
        <?php else: ?>
            updateLocationInfo(startLng, startLat);
        <?php endif; ?>
    }


    function formatPlaceName(feature) {
        if (!feature) return "";
        const mainName = feature.text || "";
        const fullName = feature.place_name || "";
        let parts = fullName.split(',').map(part => part.trim());
        let uniqueParts = [];
        let seen = new Set();

        parts.forEach(part => {
            let lowerPart = part.toLowerCase();
            if (!seen.has(lowerPart)) {
                seen.add(lowerPart);
                uniqueParts.push(part);
            }
        });

        if (uniqueParts.length > 0 && mainName) {
            if (uniqueParts[0].toLowerCase() !== mainName.toLowerCase()) {

                const index = uniqueParts.findIndex(p => p.toLowerCase() === mainName.toLowerCase());
                if (index === -1) {
                    uniqueParts.unshift(mainName);
                }
            }
        }

        return uniqueParts.join(', ');
    }


    function updateLocationInfo(lng, lat, forcedName = null) {
        selectedLat = lat;
        selectedLon = lng;

        if (forcedName) {
            let textVal, placeNameVal;

            if (typeof forcedName === 'object') {
                textVal = forcedName.text;
                placeNameVal = forcedName.features ? forcedName.features[0].place_name : forcedName.place_name;
            } else {
                textVal = forcedName.split(',')[0];
                placeNameVal = forcedName;
            }

            const formattedName = formatPlaceName({
                text: textVal,
                place_name: placeNameVal
            });

            selectedName = formattedName;
            document.getElementById('selected-loc-name').innerText = formattedName;
            return;
        }

        document.getElementById('selected-loc-name').innerText = "Mencari nama...";
        const url = `https://api.mapbox.com/geocoding/v5/mapbox.places/${lng},${lat}.json?access_token=${mapboxgl.accessToken}&types=poi,address,neighborhood,locality,place,district&language=id`;
        fetch(url).then(response => response.json()).then(data => {
            if (data.features.length > 0) {
                const feature = data.features.find(f => f.place_type.includes('poi')) || data.features[0];
                const cleanName = formatPlaceName(feature);
                selectedName = cleanName;
                document.getElementById('selected-loc-name').innerText = cleanName;
            } else {
                document.getElementById('selected-loc-name').innerText = `Koordinat: ${lat.toFixed(4)}, ${lng.toFixed(4)}`;
                selectedName = null;
            }
        }).catch(err => {
            document.getElementById('selected-loc-name').innerText = `Koordinat: ${lat.toFixed(4)}, ${lng.toFixed(4)}`;
        });
    }

    function searchMapbox() {
        const query = document.getElementById('mapbox-search-input').value;
        if (!query) return;

        const btn = document.getElementById('btn-search-mapbox');
        const originalIcon = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        btn.disabled = true;

        const center = map.getCenter();
        const url = `https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(query)}.json?access_token=${mapboxgl.accessToken}&language=id&proximity=${center.lng},${center.lat}&limit=1`;

        fetch(url).then(response => response.json()).then(data => {
                if (data.features.length > 0) {
                    const result = data.features[0];
                    const [lng, lat] = result.center;

                    map.flyTo({
                        center: [lng, lat],
                        zoom: 15
                    });

                    marker.setLngLat([lng, lat]);

                    updateLocationInfo(lng, lat, result);
                } else {
                    alert("Lokasi tidak ditemukan. Coba tambahkan nama negara (contoh: 'Paris, Prancis').");
                }
            }).catch(err => {
                console.error(err);
                alert("Gagal mencari lokasi.");
            })
            .finally(() => {
                btn.innerHTML = originalIcon;
                btn.disabled = false;
            });
    }

    document.getElementById('btn-current-loc').addEventListener('click', function() {
        const btn = this;
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm text-dark"></span>';
        btn.disabled = true;
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition((position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                map.flyTo({
                    center: [lng, lat],
                    zoom: 16
                });
                marker.setLngLat([lng, lat]);
                updateLocationInfo(lng, lat);
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }, (error) => {
                alert("Gagal mendapatkan lokasi GPS: " + error.message);
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }, {
                enableHighAccuracy: true,
                timeout: 20000,
                maximumAge: 0
            });
        } else {
            alert("Browser tidak mendukung GPS.");
        }
    });

    document.getElementById('mapbox-search-input').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchMapbox();
        }
    });
    document.getElementById('btn-search-mapbox').addEventListener('click', searchMapbox);
    document.getElementById('btn-select-location').addEventListener('click', function() {
        if (selectedLat && selectedLon) {
            const nameParam = selectedName ? `&name=${encodeURIComponent(selectedName)}` : '';
            window.location.href = `index.php?page=home&lat=${selectedLat}&lon=${selectedLon}${nameParam}`;
        }
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('weatherChart');
        if (ctx) {
            const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(255, 216, 3, 0.5)');
            gradient.addColorStop(1, 'rgba(255, 216, 3, 0)');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($chartData['labels']); ?>,
                    datasets: [{
                        label: 'Suhu (°C)',
                        data: <?php echo json_encode($chartData['data']); ?>,
                        borderColor: '#272343',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#ffd803',
                        pointBorderColor: '#272343',
                        pointRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            grid: {
                                color: 'rgba(39, 35, 67, 0.1)',
                                borderDash: [5, 5]
                            },
                            ticks: {
                                color: '#2d334a',
                                callback: val => val.toFixed(1) + '°C'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#2d334a'
                            }
                        }
                    }
                }
            });
        }

        // Auto Geolocation
        const loadingState = document.getElementById('loading-state');
        const manualState = document.getElementById('manual-state');

        if (loadingState && navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                const token = '<?php echo $_ENV['MAPBOX_ACCESS_TOKEN'] ?? ''; ?>';
                const url = `https://api.mapbox.com/geocoding/v5/mapbox.places/${lng},${lat}.json?access_token=${token}&types=poi,address,neighborhood,locality,place,district&language=id`;

                fetch(url).then(res => res.json()).then(data => {
                    let locName = '';
                    if (data.features.length > 0) {
                        const feature = data.features[0];
                        locName = formatPlaceName(feature);
                    }
                    const nameParam = locName ? `&name=${encodeURIComponent(locName)}` : '';
                    window.location.href = `index.php?page=home&lat=${lat}&lon=${lng}${nameParam}`;
                }).catch(() => {
                    window.location.href = `index.php?page=home&lat=${lat}&lon=${lng}`;
                });

            }, function(error) {
                console.warn("Geo error:", error);
                if (loadingState) loadingState.style.display = 'none';
                if (manualState) manualState.style.display = 'block';
            }, {
                enableHighAccuracy: true,
                timeout: 20000,
                maximumAge: 0
            });
        } else if (loadingState) {
            loadingState.style.display = 'none';
            manualState.style.display = 'block';
        }

        const headerForm = document.querySelector('nav form');
        if (headerForm) {
            headerForm.addEventListener('submit', function(e) {
                e.preventDefault();
            });
        }
    });
</script>

<?php include 'layout/footer.php'; ?>