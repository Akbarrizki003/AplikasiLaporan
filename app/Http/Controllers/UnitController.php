<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;
use Illuminate\Support\Facades\Hash;


class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::all();
        return view('units.index', compact('units'));
    }

    // Menampilkan form tambah unit
    public function create()
    {
        return view('units.create');
    }

    // Menyimpan data unit baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_unit' => 'required|string|max:255',
            'direktur' => 'required|string|max:255',
            'email' => 'required|email|unique:tb_unit,email',
            'telepon' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except('password', 'password_confirmation', 'logo');
        $data['password'] = Hash::make($request->password);

        // Upload logo jika ada
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = time() . '_' . $logo->getClientOriginalName();
            $logo->move(public_path('uploads/logo'), $logoName);
            $data['logo'] = 'uploads/logo/' . $logoName;
        }

        Unit::create($data);

        return redirect()->route('units.index')->with('success', 'Unit berhasil ditambahkan.');
    }

    // Menampilkan form edit unit
    public function edit($id)
    {
        $unit = Unit::findOrFail($id);
        return view('units.edit', compact('unit'));
    }

    // Menyimpan update unit
    public function update(Request $request, $id)
    {
        $unit = Unit::findOrFail($id);

        $request->validate([
            'nama_unit' => 'required|string|max:255',
            'direktur' => 'required|string|max:255',
            'email' => 'required|email|unique:tb_unit,email,' . $unit->id_unit . ',id_unit',
            'telepon' => 'required|string|max:20',
            'password' => 'nullable|string|min:6|confirmed',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except('password', 'password_confirmation', 'logo');

        // Jika password diisi, update password
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Jika ada file logo baru
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = time() . '_' . $logo->getClientOriginalName();
            $logo->move(public_path('uploads/logo'), $logoName);
            $data['logo'] = 'uploads/logo/' . $logoName;
        }

        $unit->update($data);

        return redirect()->route('units.index')->with('success', 'Unit berhasil diperbarui.');
    }
}
