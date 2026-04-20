<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::withCount('users')->latest()->get();
        return view('admin.positions.index', compact('positions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:positions,name',
        ]);

        Position::create($request->all());

        return redirect()->route('admin.positions.index')->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function update(Request $request, Position $position)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:positions,name,' . $position->id,
        ]);

        $position->update($request->all());

        return redirect()->route('admin.positions.index')->with('success', 'Jabatan berhasil diupdate.');
    }

    public function destroy(Position $position)
    {
        if ($position->users()->count() > 0) {
            return redirect()->route('admin.positions.index')->with('error', 'Jabatan tidak bisa dihapus karena masih digunakan oleh karyawan.');
        }

        $position->delete();
        return redirect()->route('admin.positions.index')->with('success', 'Jabatan berhasil dihapus.');
    }
}
