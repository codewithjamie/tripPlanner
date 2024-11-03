<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location Search</title>
    <link rel="shortcut icon" href="https://d19m59y37dris4.cloudfront.net/directory/2-0-2/img/favicon.png">
    <link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/directory/2-0-2/vendor/nouislider/nouislider.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Playfair+Display:400,400i,700">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,400i,700">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.4.1/css/swiper.min.css">
    <link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/directory/2-0-2/vendor/magnific-popup/magnific-popup.css">
    <link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/directory/2-0-2/css/style.default.2018ba20.css" id="theme-stylesheet">
    <link rel="stylesheet" href="https://d19m59y37dris4.cloudfront.net/directory/2-0-2/css/custom.0a822280.css">
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/v2.10.0/mapbox-gl.css">
    <link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.2/mapbox-gl-geocoder.css">
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.10.0/mapbox-gl.js"></script>
    <script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.2/mapbox-gl-geocoder.min.js"></script>
    <link rel="stylesheet" href="/output.css">
</head>
<body class="antialiased" style="background-image:url(https://roadtrippers.com/wp-content/uploads/2024/04/autopilot_travel2.webp);">
    <div class="container-fluid">
        <div class="pt-4 mt-4"></div>

        <div class="container pt-4 mt-4">
            <div class="container py-6 py-md-7 text-white z-index-20">
                <div class="row">
                    <div class="col-xl-10">
                        <div class="text-center text-lg-start">
                            <p class="subtitle letter-spacing-4 mb-2 text-secondary text-shadow">A road trip planner</p>
                            <h1 class="display-3 fw-bold text-shadow text-dark">That plans your trip for you</h1>
                        </div>
                        <div class="search-bar mt-5 p-3 p-lg-1 ps-lg-4">
                            <div class="row">
                                <div class="col-lg-10 d-flex align-items-center form-group">
                                    <div id="geocoder" class="geocoder w-100"></div>
                                </div>
                                <div class="col-lg-2 d-grid">
                                    <button class="btn btn-outline-primary rounded-pill h-100" type="button" onclick="searchNow()">Go</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-top: 30%"></div>
        <div class="container card bg-dark">
            <div class="card-body pb-2 mb-4 pt-2 mt-4">
                <footer class="text-center text-white">
                    <h4>Handcrafted by <a href="https://github.com/codewithjamie" class="text-warning" target="_blank">CodewithJamie</a>.</h4>
                </footer>
            </div>
        </div>
    </div>

    <script>
        mapboxgl.accessToken = '{{ env("MAPBOX_ACCESS_TOKEN") }}';

        // Initialize Geocoder
        const geocoder = new MapboxGeocoder({
            accessToken: mapboxgl.accessToken,
            mapboxgl: mapboxgl,
            marker: false
        });

        // Append geocoder to the container
        document.getElementById('geocoder').appendChild(geocoder.onAdd());

        // Handle location selection through event listener
        geocoder.on('result', function (e) {
            const location = e.result;
            const data = {
                name: location.place_name,
                latitude: location.geometry.coordinates[1],
                longitude: location.geometry.coordinates[0]
            };

            // Store the location data in session storage
            sessionStorage.setItem('initialLocation', JSON.stringify(data));
        });

        function searchNow() {
            const inputField = document.querySelector('.mapboxgl-ctrl-geocoder--input'); // Get the input field from the geocoder
            const query = inputField.value.trim();

            if (query) {
                // Trigger geocoder search with user input
                geocoder.query(query);

                // After a brief timeout, redirect if location data is available
                setTimeout(() => {
                    const initialLocation = sessionStorage.getItem('initialLocation');
                    if (initialLocation) {
                        window.location.href = '/map';
                    } else {
                        alert('No location found. Please try a different query.');
                    }
                }, 500); // Adjust the timeout duration if needed
            } else {
                alert('Please enter a location.');
            }
        }
    </script>
</body>
</html>
