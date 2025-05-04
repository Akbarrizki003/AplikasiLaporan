<?php

namespace App\Http\Controllers;

use App\Models\Dokumen;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Mail\NotifikasiDokumen;
use Carbon\Carbon;

class DokumenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Cek role user yang sedang login
        $user = Auth::user();
        $role = $user->role; // Asumsikan ada kolom role di tabel users

        // Ambil dokumen berdasarkan role
        if ($role === 'unit') {
            // Unit melihat dokumen miliknya sendiri
            $dokumen = Dokumen::where('id_unit', $user->id_unit)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } elseif ($role === 'keuangan') {
            // Keuangan melihat semua dokumen yang statusnya 'dikirim' atau 'diterima_keuangan'
            $dokumen = Dokumen::whereIn('status', ['dikirim', 'diterima_keuangan'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } elseif ($role === 'manajer') {
            // Manajer melihat dokumen yang statusnya 'diteruskan_ke_manejer', 'disetujui_manejer', 'ditolak_manejer'
            $dokumen = Dokumen::whereIn('status', ['diteruskan_ke_manejer', 'disetujui_manejer', 'ditolak_manejer'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } elseif ($role === 'atasan') {
            // Atasan melihat dokumen yang statusnya 'diteruskan_ke_atasan', 'disetujui_atasan', 'ditolak_atasan'
            $dokumen = Dokumen::whereIn('status', ['diteruskan_ke_atasan', 'disetujui_atasan', 'ditolak_atasan'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            // Admin melihat semua dokumen
            $dokumen = Dokumen::orderBy('created_at', 'desc')->paginate(10);
        }

        return view('dokumen.index', compact('dokumen'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Pastikan hanya unit yang bisa membuat dokumen
        if (Auth::user()->role !== 'unit') {
            return redirect()->route('dokumen.index')
                ->with('error', 'Anda tidak memiliki akses untuk membuat dokumen');
        }

        $units = Unit::all();
        return view('dokumen.create', compact('units'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validasi request
        $validator = Validator::make($request->all(), [
            'nama_dokumen' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Upload file
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('dokumen', $fileName, 'public');

        // Simpan dokumen
        $dokumen = Dokumen::create([
            'id_unit' => Auth::user()->id_unit,
            'nama_dokumen' => $request->nama_dokumen,
            'tanggal_upload' => Carbon::now()->toDateString(),
            'file' => $filePath,
            'status' => 'dikirim',
        ]);

        // Kirim notifikasi ke bagian keuangan
        $this->kirimNotifikasiKeKeuangan($dokumen);

        return redirect()->route('dokumen.index')
            ->with('success', 'Dokumen berhasil diunggah dan dikirim ke bagian keuangan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $dokumen = Dokumen::findOrFail($id);
        $user = Auth::user();
        
        // Jika role adalah keuangan, manajer, atau atasan, tampilkan view dokumen
        if (in_array($user->role, ['keuangan', 'manajer', 'atasan'])) {
            return $this->viewDokumen($id);
        } else {
            return view('dokumen.show', compact('dokumen'));
        }
    }

    /**
     * View file dokumen langsung di browser
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function viewDokumen($id)
    {
        $dokumen = Dokumen::findOrFail($id);
        
        if (!Storage::disk('public')->exists($dokumen->file)) {
            return redirect()->back()->with('error', 'File tidak ditemukan');
        }
        
        $filePath = Storage::disk('public')->path($dokumen->file);
        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
        
        // Cek ekstensi file dan tampilkan sesuai tipenya
        if (in_array($fileExtension, ['pdf'])) {
            $fileUrl = Storage::disk('public')->url($dokumen->file);
            return view('dokumen.view-pdf', compact('dokumen', 'fileUrl'));
        } elseif (in_array($fileExtension, ['doc', 'docx', 'xls', 'xlsx'])) {
            // Untuk file office, kita akan menggunakan view khusus dengan iframe
            $fileUrl = Storage::disk('public')->url($dokumen->file);
            return view('dokumen.view-office', compact('dokumen', 'fileUrl', 'fileExtension'));
        } else {
            return redirect()->back()->with('error', 'Format file tidak didukung untuk dilihat langsung');
        }
    }

    /**
     * Download file dokumen - hanya untuk unit
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function download($id)
    {
        $dokumen = Dokumen::findOrFail($id);
        $user = Auth::user();
        
        // Hanya user dengan role unit yang bisa download dokumen miliknya
        if ($user->role !== 'unit' || $dokumen->id_unit !== $user->id_unit) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengunduh dokumen ini');
        }
        
        if (!Storage::disk('public')->exists($dokumen->file)) {
            return redirect()->back()->with('error', 'File tidak ditemukan');
        }
        
        return Storage::disk('public')->download($dokumen->file, $dokumen->nama_dokumen);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $dokumen = Dokumen::findOrFail($id);
        
        // Cek apakah user berhak mengedit dokumen
        $user = Auth::user();
        
        if ($user->role === 'unit' && $dokumen->id_unit !== $user->id_unit) {
            return redirect()->route('dokumen.index')
                ->with('error', 'Anda tidak memiliki akses untuk mengedit dokumen ini');
        }
        
        // Cek apakah dokumen bisa diedit berdasarkan statusnya
        $editableStatus = ['dikirim', 'ditolak_manejer', 'ditolak_atasan'];
        if ($user->role === 'unit' && !in_array($dokumen->status, $editableStatus)) {
            return redirect()->route('dokumen.index')
                ->with('error', 'Dokumen ini tidak dapat diedit karena statusnya ' . $dokumen->status_label);
        }
        
        $units = Unit::all();
        return view('dokumen.edit', compact('dokumen', 'units'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $dokumen = Dokumen::findOrFail($id);
        
        // Validasi request
        $validator = Validator::make($request->all(), [
            'nama_dokumen' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update data dokumen
        $dokumen->nama_dokumen = $request->nama_dokumen;
        
        // Jika ada file baru
        if ($request->hasFile('file')) {
            // Hapus file lama jika ada
            if ($dokumen->file && Storage::disk('public')->exists($dokumen->file)) {
                Storage::disk('public')->delete($dokumen->file);
            }
            
            // Upload file baru
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('dokumen', $fileName, 'public');
            $dokumen->file = $filePath;
        }

        // Reset status jika dokumen ditolak dan di-update kembali
        if (in_array($dokumen->status, ['ditolak_manejer', 'ditolak_atasan'])) {
            $dokumen->status = 'dikirim';
            $dokumen->catatan = null;
        }

        $dokumen->save();

        // Kirim notifikasi ke bagian keuangan jika status 'dikirim'
        if ($dokumen->status === 'dikirim') {
            $this->kirimNotifikasiKeKeuangan($dokumen);
        }

        return redirect()->route('dokumen.index')
            ->with('success', 'Dokumen berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $dokumen = Dokumen::findOrFail($id);
        
        // Cek apakah user berhak menghapus dokumen
        $user = Auth::user();
        
        if ($user->role === 'unit' && $dokumen->id_unit !== $user->id_unit) {
            return redirect()->route('dokumen.index')
                ->with('error', 'Anda tidak memiliki akses untuk menghapus dokumen ini');
        }
        
        // Hapus file dari storage
        if ($dokumen->file && Storage::disk('public')->exists($dokumen->file)) {
            Storage::disk('public')->delete($dokumen->file);
        }
        
        // Hapus dokumen
        $dokumen->delete();
        
        return redirect()->route('dokumen.index')
            ->with('success', 'Dokumen berhasil dihapus');
    }

    /**
     * Proses dokumen dari keuangan ke manajer
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function terimaKeuangan($id)
    {
        $dokumen = Dokumen::findOrFail($id);
        
        // Pastikan hanya user dengan role keuangan yang bisa melakukan ini
        if (Auth::user()->role !== 'keuangan') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk melakukan tindakan ini');
        }
        
        // Update status dokumen
        $dokumen->status = 'diterima_keuangan';
        $dokumen->save();
        
        return redirect()->route('dokumen.index')
            ->with('success', 'Dokumen berhasil diterima oleh keuangan');
    }

    /**
     * Teruskan dokumen dari keuangan ke manajer
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function teruskanKeManajer($id)
    {
        $dokumen = Dokumen::findOrFail($id);
        
        // Pastikan hanya user dengan role keuangan yang bisa melakukan ini
        if (Auth::user()->role !== 'keuangan') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk melakukan tindakan ini');
        }
        
        // Update status dokumen
        $dokumen->status = 'diteruskan_ke_manejer';
        $dokumen->save();
        
        // Kirim notifikasi ke manajer
        $this->kirimNotifikasiKeManajer($dokumen);
        
        return redirect()->route('dokumen.index')
            ->with('success', 'Dokumen berhasil diteruskan ke manajer');
    }

    /**
     * Manajer menyetujui dokumen
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function setujuiManajer($id)
    {
        $dokumen = Dokumen::findOrFail($id);
        
        // Pastikan hanya user dengan role manajer yang bisa melakukan ini
        if (Auth::user()->role !== 'manajer') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk melakukan tindakan ini');
        }
        
        // Update status dokumen
        $dokumen->status = 'disetujui_manejer';
        $dokumen->save();
        
        return redirect()->route('dokumen.index')
            ->with('success', 'Dokumen berhasil disetujui');
    }

    /**
     * Manajer menolak dokumen
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function tolakManajer(Request $request, $id)
    {
        $dokumen = Dokumen::findOrFail($id);
        
        // Pastikan hanya user dengan role manajer yang bisa melakukan ini
        if (Auth::user()->role !== 'manajer') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk melakukan tindakan ini');
        }
        
        // Validasi input
        $request->validate([
            'catatan' => 'required|string',
        ]);
        
        // Update status dokumen
        $dokumen->status = 'ditolak_manejer';
        $dokumen->catatan = $request->catatan;
        $dokumen->save();
        
        return redirect()->route('dokumen.index')
            ->with('success', 'Dokumen ditolak dengan catatan');
    }

    /**
     * Teruskan dokumen dari manajer ke atasan
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function teruskanKeAtasan($id)
    {
        $dokumen = Dokumen::findOrFail($id);
        
        // Pastikan hanya user dengan role manajer yang bisa melakukan ini
        if (Auth::user()->role !== 'manajer') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk melakukan tindakan ini');
        }
        
        // Pastikan dokumen sudah disetujui manajer
        if ($dokumen->status !== 'disetujui_manejer') {
            return redirect()->back()->with('error', 'Dokumen harus disetujui terlebih dahulu');
        }
        
        // Update status dokumen
        $dokumen->status = 'diteruskan_ke_atasan';
        $dokumen->save();
        
        // Kirim notifikasi ke atasan
        $this->kirimNotifikasiKeAtasan($dokumen);
        
        return redirect()->route('dokumen.index')
            ->with('success', 'Dokumen berhasil diteruskan ke atasan');
    }

    /**
     * Atasan menyetujui dokumen
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function setujuiAtasan($id)
    {
        $dokumen = Dokumen::findOrFail($id);
        
        // Pastikan hanya user dengan role atasan yang bisa melakukan ini
        if (Auth::user()->role !== 'atasan') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk melakukan tindakan ini');
        }
        
        // Update status dokumen
        $dokumen->status = 'disetujui_atasan';
        $dokumen->save();
        
        return redirect()->route('dokumen.index')
            ->with('success', 'Dokumen berhasil disetujui');
    }

    /**
     * Atasan menolak dokumen
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function tolakAtasan(Request $request, $id)
    {
        $dokumen = Dokumen::findOrFail($id);
        
        // Pastikan hanya user dengan role atasan yang bisa melakukan ini
        if (Auth::user()->role !== 'atasan') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk melakukan tindakan ini');
        }
        
        // Validasi input
        $request->validate([
            'catatan' => 'required|string',
        ]);
        
        // Update status dokumen
        $dokumen->status = 'ditolak_atasan';
        $dokumen->catatan = $request->catatan;
        $dokumen->save();
        
        return redirect()->route('dokumen.index')
            ->with('success', 'Dokumen ditolak dengan catatan');
    }

    /**
     * Kirim notifikasi ke keuangan
     */
    private function kirimNotifikasiKeKeuangan($dokumen)
    {
        // Ambil semua user dengan role keuangan
        $keuanganUsers = User::where('role', 'keuangan')->get();
        
        foreach ($keuanganUsers as $user) {
            // Kirim email notifikasi
            Mail::to($user->email)->send(new NotifikasiDokumen(
                'Dokumen Baru Diterima',
                'Terdapat dokumen baru dari unit ' . $dokumen->unit->nama_unit,
                route('dokumen.show', $dokumen->id_dokumen),
                $user
            ));
        }
    }

    /**
     * Kirim notifikasi ke manajer
     */
    private function kirimNotifikasiKeManajer($dokumen)
    {
        // Ambil semua user dengan role manajer
        $manajerUsers = User::where('role', 'manajer')->get();
        
        // Generate temporary password untuk manajer
        $tempPassword = Str::random(10);
        
        foreach ($manajerUsers as $user) {
            // Update password user
            $user->password = bcrypt($tempPassword);
            $user->save();
            
            // Kirim email notifikasi dengan kredensial login
            Mail::to($user->email)->send(new NotifikasiDokumen(
                'Dokumen Untuk Disetujui',
                'Terdapat dokumen dari unit ' . $dokumen->unit->nama_unit . ' yang perlu disetujui',
                route('dokumen.show', $dokumen->id_dokumen),
                $user,
                $user->email,
                $tempPassword
            ));
        }
    }

    /**
     * Kirim notifikasi ke atasan
     */
    private function kirimNotifikasiKeAtasan($dokumen)
    {
        // Ambil semua user dengan role atasan
        $atasanUsers = User::where('role', 'atasan')->get();
        
        // Generate temporary password untuk atasan
        $tempPassword = Str::random(10);
        
        foreach ($atasanUsers as $user) {
            // Update password user
            $user->password = bcrypt($tempPassword);
            $user->save();
            
            // Kirim email notifikasi dengan kredensial login
            Mail::to($user->email)->send(new NotifikasiDokumen(
                'Dokumen Untuk Persetujuan Final',
                'Terdapat dokumen dari unit ' . $dokumen->unit->nama_unit . ' yang sudah disetujui manajer dan perlu persetujuan final',
                route('dokumen.show', $dokumen->id_dokumen),
                $user,
                $user->email,
                $tempPassword
            ));
        }
    }
}