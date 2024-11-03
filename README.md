# Map with Destinations & POIs

A web application that allows users to interact with a map, select destinations, and view points of interest (POIs) such as restaurants, gas stations, and hotels along their route.

## Prerequisites

Make sure you have the following software installed:

- PHP >= 8.0
- Composer

## Installation

### 1. Clone the repository:
   git clone https://github.com/yourusername/yourproject.git

### 2. Navigate to the project directory:
    cd yourproject.

### 3. Install PHP dependencies: 
    composer install

### 4. Copy the .env.example file to .env:
    cp .env.example .env

### 5. Generate the application key:
    php artisan key:generate

### 6. Mapbox API Key: You will need to sign up for a Mapbox account and obtain an access token. Add your Mapbox access token to your .env file:
MAPBOX_ACCESS_TOKEN=your_mapbox_access_token

### 7. php artisan serve
Your application will be accessible at http://localhost:8000.

## Usage

1. Open your web browser and navigate to http://localhost:8000.
2. Select your starting location and click on the map to add destinations.
3. Use the sidebar to view selected destinations and calculate the route.
4. Explore the points of interest along the route displayed on the map.


## Features

1. Interactive map powered by Mapbox.
2. Ability to select multiple destinations and calculate the route.
3. Displays total distance and travel time for the selected route.
4. Shows points of interest (POIs) like restaurants, gas stations, and hotels along the route.
5. Prevents adding the same destination multiple times.





