<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Dokumen;
use App\Models\Disposisi;

class DashboardController extends Controller
{
    public function index()
    {
        // Cek jika user adalah admin
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            
            if ($user->isKeuangan()) {
                // Dashboard untuk keuangan
                $dokumen = Dokumen::where('status', 'dikirim')
                    ->orWhere('status', 'diterima_keuangan')
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                return view('dashboard.keuangan', compact('dokumen'));
            } elseif ($user->isManajer()) {
                // Dashboard untuk manajer
                $dokumen = Dokumen::where('status', 'diteruskan_ke_manejer')
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                return view('dashboard.manajer', compact('dokumen'));
            } elseif ($user->isAtasan()) {
                // Dashboard untuk atasan
                $dokumen = Dokumen::where('status', 'diteruskan_ke_atasan')
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                return view('dashboard.atasan', compact('dokumen'));
            }
        } 
        // Cek jika user adalah unit
        elseif (Auth::guard('unit')->check()) {
            $unit = Auth::guard('unit')->user();
            $dokumen = Dokumen::where('id_unit', $unit->id_unit)
                ->orderBy('created_at', 'desc')
                ->get();
            
            return view('dashboard.unit', compact('dokumen'));
        }
        
        return redirect()->route('login');
    }
}