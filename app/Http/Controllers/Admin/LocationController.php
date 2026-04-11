<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $locations = CompanySetting::latest()->get();
        return view('admin.locations', compact('locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|numeric',
        ]);

        CompanySetting::create($request->all());
        return redirect()->route('admin.locations.index')->with('success', 'Lokasi berhasil ditambahkan!');
    }

    public function update(Request $request, CompanySetting $location)
    {
        $request->validate([
            'name' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|numeric',
        ]);

        $location->update($request->all());
        return redirect()->route('admin.locations.index')->with('success', 'Lokasi berhasil diupdate!');
    }

    public function destroy(CompanySetting $location)
    {
        $location->delete();
        return redirect()->route('admin.locations.index')->with('success', 'Lokasi berhasil dihapus!');
    }
}
