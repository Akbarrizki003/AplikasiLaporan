<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UnitController extends Controller
{
    /**
     * Display a listing of the units.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $units = Unit::with('user')->get();
        return view('units.index', compact('units'));
    }

    /**
     * Show the form for creating a new unit.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $users = User::where('role', 'unit')
                      ->whereDoesntHave('unit')
                      ->get();
        
        return view('units.create', compact('users'));
    }

    /**
     * Store a newly created unit in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_unit' => 'required|string|max:255',
            'direktur' => 'required|string|max:255',
            'id_user' => 'required|exists:users,id',
            'telepon' => 'required|string|max:15',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $unit = new Unit();
        $unit->nama_unit = $request->nama_unit;
        $unit->direktur = $request->direktur;
        $unit->id_user = $request->id_user;
        $unit->telepon = $request->telepon;

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('public/unit_logos');
            $unit->logo = str_replace('public/', '', $logoPath);
        }

        $unit->save();

        return redirect()->route('units.index')
            ->with('success', 'Unit berhasil dibuat!');
    }

    /**
     * Display the specified unit.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $unit = Unit::with('user')->findOrFail($id);
        return view('units.show', compact('unit'));
    }

    /**
     * Show the form for editing the specified unit.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $unit = Unit::findOrFail($id);
        $users = User::where('role', 'unit')
                      ->where(function($query) use ($unit) {
                          $query->whereDoesntHave('unit')
                                ->orWhereHas('unit', function($q) use ($unit) {
                                    $q->where('id_unit', $unit->id_unit);
                                });
                      })
                      ->get();

        return view('units.edit', compact('unit', 'users'));
    }

    /**
     * Update the specified unit in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_unit' => 'required|string|max:255',
            'direktur' => 'required|string|max:255',
            'id_user' => 'required|exists:users,id',
            'telepon' => 'required|string|max:15',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $unit = Unit::findOrFail($id);
        $unit->nama_unit = $request->nama_unit;
        $unit->direktur = $request->direktur;
        $unit->id_user = $request->id_user;
        $unit->telepon = $request->telepon;

        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada
            if ($unit->logo && Storage::exists('public/' . $unit->logo)) {
                Storage::delete('public/' . $unit->logo);
            }
            
            $logoPath = $request->file('logo')->store('public/unit_logos');
            $unit->logo = str_replace('public/', '', $logoPath);
        }

        $unit->save();

        return redirect()->route('units.index')
            ->with('success', 'Unit berhasil diperbarui!');
    }

    /**
     * Remove the specified unit from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $unit = Unit::findOrFail($id);
        
        // Hapus logo jika ada
        if ($unit->logo && Storage::exists('public/' . $unit->logo)) {
            Storage::delete('public/' . $unit->logo);
        }
        
        $unit->delete();

        return redirect()->route('units.index')
            ->with('success', 'Unit berhasil dihapus!');
    }

    /**
     * Show profile form for unit user.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        $user = Auth::user();
        
        if ($user->role !== 'unit') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }
        
        $unit = $user->unit;
        
        if (!$unit) {
            $unit = new Unit();
            $unit->id_user = $user->id;
            $unit->save();
        }
        
        return view('unit.profile', compact('unit'));
    }

    /**
     * Update unit profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'unit') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }
        
        $validator = Validator::make($request->all(), [
            'nama_unit' => 'required|string|max:255',
            'direktur' => 'required|string|max:255',
            'telepon' => 'required|string|max:15',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $unit = $user->unit;
        
        if (!$unit) {
            $unit = new Unit();
            $unit->id_user = $user->id;
        }
        
        $unit->nama_unit = $request->nama_unit;
        $unit->direktur = $request->direktur;
        $unit->telepon = $request->telepon;

        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada
            if ($unit->logo && Storage::exists('public/' . $unit->logo)) {
                Storage::delete('public/' . $unit->logo);
            }
            
            $logoPath = $request->file('logo')->store('public/unit_logos');
            $unit->logo = str_replace('public/', '', $logoPath);
        }

        $unit->save();

        return redirect()->route('dokumen.create')
            ->with('success', 'Profil unit berhasil diperbarui!');
    }
}