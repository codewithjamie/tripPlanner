<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="https://d19m59y37dris4.cloudfront.net/directory/2-0-2/img/favicon.png">
    <title>Map with Destinations & POIs</title>
    <link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/v2.10.0/mapbox-gl.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.10.0/mapbox-gl.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link rel="stylesheet" href="/output.css">
    <style>
        #map {
            width: 100%;
            height: 500px;
        }

        .sidebar {
            max-height: 500px;
            overflow-y: auto;
        }

        .marker-label {
            background-color: white;
            border-radius: 3px;
            padding: 5px;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <h2 class="mt-4 mb-4">Map with Destinations, Restaurants, Gas Stations, and Hotels</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card sidebar">
                    <div class="card-body">
                        <h5 class="card-title">Destinations</h5>
                        <ul id="destination-list" class="list-group"></ul>
                        <button class="btn btn-primary mt-3" onclick="calculateRoute()">Calculate Route</button>
                        <div id="summary" class="mt-3">
                            <p>Total Distance: <span id="total-distance"></span> km</p>
                            <p>Total Time: <span id="total-time"></span> hours</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div id="map"></div>
            </div>
        </div>
    </div>

    <script>
        mapboxgl.accessToken = '{{ env("MAPBOX_ACCESS_TOKEN") }}';

        // Retrieving initial location data from session storage
        const initialLocation = JSON.parse(sessionStorage.getItem('initialLocation'));
        let destinations = initialLocation ? [initialLocation] : [];

        // Initializing map centered on initial location
        let map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/streets-v11',
            center: [initialLocation.longitude, initialLocation.latitude],
            zoom: 10
        });

        // Adding initial marker
        addMarker(initialLocation, 0);

        // providing a method to add a marker and popup for each destination
        function addMarker(destination, index) {
            // Creating a custom marker element
            const markerElement = document.createElement('div');
            markerElement.className = 'marker';
            const label = document.createElement('div');
            label.className = 'marker-label';
            label.textContent = destination.name;
            markerElement.appendChild(label);

            const marker = new mapboxgl.Marker(markerElement)
                .setLngLat([destination.longitude, destination.latitude])
                .addTo(map);

            // Popup for clicked location
            const popup = new mapboxgl.Popup({ offset: 25 })
                .setText(`Place Name: ${destination.name}\nLatitude: ${destination.latitude.toFixed(4)}\nLongitude: ${destination.longitude.toFixed(4)}`);

            marker.setPopup(popup);
            renderDestinationList();
        }

        // Adding a destination on map click
        map.on('click', function(e) {
            const destination = {
                name: `Clicked Location ${destinations.length + 1}`,
                latitude: e.lngLat.lat,
                longitude: e.lngLat.lng
            };

            // Checking to see if the clicked location is already in destinations
            if (!destinations.some(dest => dest.longitude === destination.longitude && dest.latitude === destination.latitude)) {
                destinations.push(destination);
                addMarker(destination, destinations.length - 1);

                // Showing popup on the marker after it's added
                new mapboxgl.Popup({ offset: 25 })
                    .setLngLat([destination.longitude, destination.latitude])
                    .setText(`Place Name: ${destination.name}\nLatitude: ${destination.latitude.toFixed(4)}\nLongitude: ${destination.longitude.toFixed(4)}`)
                    .addTo(map);
            } else {
                alert('This location has already been added to your destinations.');
            }
        });

        // Rendering the destination list
        function renderDestinationList() {
            const list = document.getElementById('destination-list');
            list.innerHTML = '';
            destinations.forEach((dest, index) => {
                const item = document.createElement('li');
                item.className = 'list-group-item';
                item.textContent = `${dest.name} (${dest.latitude.toFixed(4)}, ${dest.longitude.toFixed(4)})`;
                list.appendChild(item);
            });
        }

        // Fetching POIs along each segment of the route
        async function fetchPOIs(coordinate) {
            const categories = ['restaurant', 'gas_station', 'lodging'];
            const promises = categories.map(async category => {
                const response = await axios.get('https://api.mapbox.com/geocoding/v5/mapbox.places/' + category + '.json', {
                    params: {
                        access_token: mapboxgl.accessToken,
                        proximity: `${coordinate.longitude},${coordinate.latitude}`,
                        limit: 3
                    }
                });
                return response.data.features.map(place => ({
                    name: place.text,
                    address: place.place_name,
                    type: category,
                    coordinates: place.geometry.coordinates
                }));
            });
            const results = await Promise.all(promises);
            return results.flat();
        }

        // Displaying POIs on the map
        async function displayPOIsAlongRoute(routeCoordinates) {
            for (const coordinate of routeCoordinates) {
                const pois = await fetchPOIs({ longitude: coordinate[0], latitude: coordinate[1] });
                pois.forEach(poi => {
                    const marker = new mapboxgl.Marker({ color: poi.type === 'restaurant' ? 'red' : (poi.type === 'gas_station' ? 'blue' : 'green') })
                        .setLngLat(poi.coordinates)
                        .setPopup(new mapboxgl.Popup().setText(`${poi.name} (${poi.type}) - ${poi.address}`))
                        .addTo(map);
                });
            }
        }

        // Calculating the route and display the travel time and distance
        async function calculateRoute() {
            if (destinations.length < 2) {
                alert("Add at least two destinations to calculate a route.");
                return;
            }

            const coordinates = destinations.map(dest => `${dest.longitude},${dest.latitude}`).join(';');

            try {
                const response = await axios.get(`https://api.mapbox.com/directions/v5/mapbox/driving/${coordinates}`, {
                    params: {
                        access_token: mapboxgl.accessToken,
                        geometries: 'geojson',
                        overview: 'full'
                    }
                });

                const route = response.data.routes[0];
                document.getElementById('total-distance').innerText = (route.distance / 1000).toFixed(2);
                document.getElementById('total-time').innerText = (route.duration / 3600).toFixed(2);

                // Displaying each trip's time and distance
                route.legs.forEach((leg, index) => {
                    const list = document.getElementById('destination-list');
                    const item = document.createElement('li');
                    item.className = 'list-group-item';
                    item.textContent = `Trip ${index + 1} distance: ${leg.duration / 60} min, ${leg.distance / 1000} km`;
                    list.appendChild(item);
                });

                // Displaying the route on the map
                if (map.getSource('route')) {
                    map.getSource('route').setData(route.geometry);
                } else {
                    map.addLayer({
                        id: 'route',
                        type: 'line',
                        source: {
                            type: 'geojson',
                            data: route.geometry
                        },
                        layout: {
                            'line-join': 'round',
                            'line-cap': 'round'
                        },
                        paint: {
                            'line-color': '#3887be',
                            'line-width': 5,
                            'line-opacity': 0.75
                        }
                    });
                }

                // Displaying the points of interest along the route
                await displayPOIsAlongRoute(route.geometry.coordinates);
            } catch (error) {
                console.error("Error calculating route:", error);
                alert("Error calculating route. Please try again.");
            }
        }

        // rendering the initial location into list
        renderDestinationList();
    </script>
</body>

</html>
