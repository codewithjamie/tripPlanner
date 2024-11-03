<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Stevebauman\Location\Facades\Location;
use Jenssegers\Agent\Facades\Agent;

class DestinationController extends Controller
{
    public function index(Request $request) {
        $whoIsIp = $request->ip();
        $position = Location::get($whoIsIp);

        $getCountry = isset($position->countryName) ? $position->countryName : "Unknown Country";
        $getCityName = isset($position->cityName) ? $position->cityName : "Unknown City";
        $getLatitude = isset($position->latitude) ? $position->latitude : null;
        $getLongitude = isset($position->longitude) ? $position->longitude : null;

        return view('destination', compact('getCountry', 'getCityName', 'getLatitude', 'getLongitude'));
    }


    // Store a new destination (optional, if using a database)
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'order' => 'required|integer',
        ]);

        $destination = Destination::create($data);
        return response()->json($destination, 201);
    }

    // Get route details from Mapbox Directions API
    public function getRouteDetails(Request $request)
    {
        $destinations = $request->destinations;
        $coordinates = implode(';', array_map(function ($d) {
            return "{$d['longitude']},{$d['latitude']}";
        }, $destinations));

        $response = Http::get("https://api.mapbox.com/directions/v5/mapbox/driving/{$coordinates}", [
            'access_token' => env('MAPBOX_ACCESS_TOKEN'),
            'geometries' => 'geojson',
            'overview' => 'full'
        ]);

        return response()->json($response->json());
    }
}
