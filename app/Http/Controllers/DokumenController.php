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
use App\Mail\DokumenNotification;
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
        // Filter dokumen berdasarkan role
        $user = Auth::user();
        $query = Dokumen::query();
        
        if ($user->role === 'unit') {
            // User dengan role unit hanya melihat dokumen miliknya
            $id_unit = $user->id_unit; // Menggunakan accessor getIdUnitAttribute
            
            if (!$id_unit) {
                return redirect()->route('unit.profile.create')
                    ->with('error', 'Anda perlu melengkapi profil unit terlebih dahulu.');
            }
            
            $query->where('id_unit', $id_unit);
        } elseif ($user->role === 'keuangan') {
            // Bagian keuangan melihat semua dokumen
            // Tidak perlu filter khusus
        } elseif ($user->role === 'manajer') {
            // Manajer melihat dokumen yang sudah diterima keuangan atau lebih tinggi
            $query->whereIn('status', [
                'diterima_keuangan', 
                'diteruskan_ke_manejer', 
                'disetujui_manejer', 
                'ditolak_manejer',
                'diteruskan_ke_atasan',
                'disetujui_atasan',
                'ditolak_atasan'
            ]);
        } elseif ($user->role === 'atasan') {
            // Atasan melihat dokumen yang sudah diteruskan ke atasan atau lebih tinggi
            $query->whereIn('status', [
                'diteruskan_ke_atasan',
                'disetujui_atasan',
                'ditolak_atasan'
            ]);
        }
        
        // Urutkan berdasarkan tanggal upload terbaru
        $dokumen = $query->orderBy('tanggal_upload', 'desc')
                        ->paginate(10);
        
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

        // Ambil id_unit dari user yang sedang login dengan role 'unit'
        $user = Auth::user();
        
        // Dapatkan id_unit melalui relasi unit
        $id_unit = null;
        if ($user->role === 'unit' && $user->unit) {
            $id_unit = $user->unit->id_unit;
        }
        
        // Jika id_unit tidak ditemukan, tampilkan error
        if (!$id_unit) {
            return redirect()->back()
                ->with('error', 'Data unit tidak ditemukan. Silakan lengkapi profil unit terlebih dahulu.')
                ->withInput();
        }

        // Simpan dokumen
        $dokumen = Dokumen::create([
            'id_unit' => $id_unit,
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
            return view('dokumen.show', compact('dokumen'));
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
        
        // Hanya user dengan role keuangan, manajer, atau atasan yang bisa melihat dokumen
        $user = Auth::user();
        if (!in_array($user->role, ['keuangan', 'manajer', 'atasan'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk melihat dokumen ini');
        }
        
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
            ->with('success', 'Dokumen berhasil diperbarui dan dikirim ke bagian keuangan');
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
            $subject = 'Dokumen Baru Diterima';
            $message = 'Terdapat dokumen baru dari unit ' . $dokumen->unit->nama_unit;
            
            Mail::to($user->email)->send(new DokumenNotification(
                $dokumen,
                $subject,
                $message
            ));
        }
    }

    /**
     * Kirim notifikasi ke manajer dengan kredensial login
     */
    private function kirimNotifikasiKeManajer($dokumen)
    {
        // Ambil semua user dengan role manajer
        $manajerUsers = User::where('role', 'manajer')->get();
        
        foreach ($manajerUsers as $user) {
            // Generate temporary password untuk manajer
            $tempPassword = Str::random(10);
            
            // Update password user
            $user->password = bcrypt($tempPassword);
            $user->save();
            
            // Siapkan data login
            $loginData = [
                'email' => $user->email,
                'password' => $tempPassword,
                'login_url' => route('login')
            ];
            
            // Kirim email notifikasi dengan kredensial login
            $subject = 'Dokumen Untuk Disetujui';
            $message = 'Terdapat dokumen dari unit ' . $dokumen->unit->nama_unit . ' yang perlu disetujui';
            
            Mail::to($user->email)->send(new DokumenNotification(
                $dokumen,
                $subject,
                $message,
                $loginData
            ));
        }
    }

    /**
     * Kirim notifikasi ke atasan dengan kredensial login
     */
    private function kirimNotifikasiKeAtasan($dokumen)
    {
        // Ambil semua user dengan role atasan
        $atasanUsers = User::where('role', 'atasan')->get();
        
        foreach ($atasanUsers as $user) {
            // Generate temporary password untuk atasan
            $tempPassword = Str::random(10);
            
            // Update password user
            $user->password = bcrypt($tempPassword);
            $user->save();
            
            // Siapkan data login
            $loginData = [
                'email' => $user->email,
                'password' => $tempPassword,
                'login_url' => route('login')
            ];
            
            // Kirim email notifikasi dengan kredensial login
            $subject = 'Dokumen Untuk Persetujuan Final';
            $message = 'Terdapat dokumen dari unit ' . $dokumen->unit->nama_unit . ' yang sudah disetujui manajer dan perlu persetujuan final';
            
            Mail::to($user->email)->send(new DokumenNotification(
                $dokumen,
                $subject,
                $message,
                $loginData
            ));
        }
    }
}