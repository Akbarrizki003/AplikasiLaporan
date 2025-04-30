<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DokumenController extends Controller
{
    public function index()
    {
        $dokumens = Dokumen::with('unit')->get();
        return view('dokumens.index', compact('dokumens'));
    }

    public function create()
    {
        $units = Unit::all();
        return view('dokumens.create', compact('units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_unit' => 'required|exists:tb_unit,id_unit',
            'nama_dokumen' => 'required|string|max:255',
            'tanggal_upload' => 'required|date',
            'file' => 'required|file|mimes:pdf,docx,doc,jpg,png|max:2048',
            'status' => 'required|in:dikirim,diterima_keuangan,diteruskan_ke_manejer,disetujui_manejer,ditolak_manejer,diteruskan_ke_atasan,disetujui_atasan,ditolak_atasan',
            'catatan' => 'nullable|string',
        ]);

        $data = $request->except('file');

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('uploads/dokumen', $filename, 'public');
            $data['file'] = 'uploads/dokumen/' . $filename;
        }

        Dokumen::create($data);

        return redirect()->route('dokumens.index')->with('success', 'Dokumen berhasil ditambahkan.');
    }
}
