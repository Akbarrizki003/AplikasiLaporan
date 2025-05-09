@extends('layouts.app')

@section('content')
<div class="flex flex-col space-y-4">
    <!-- Document Header -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-pln-blue to-pln-blue-light p-5">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                <div>
                    <h1 class="text-white text-xl md:text-2xl font-semibold">{{ $dokumen->nama_dokumen }}</h1>
                    <p class="text-white/80 text-sm mt-1 flex items-center">
                        <i class="fas fa-file-pdf mr-2"></i>Dokumen PDF
                    </p>
                </div>
                <div class="flex mt-3 md:mt-0 space-x-2">
                    <a href="{{ route('dokumen.show', $dokumen->id_dokumen) }}" class="inline-flex items-center px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white rounded-md transition-colors text-sm">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                    <a href="{{ Storage::disk('public')->url($dokumen->file) }}" download class="inline-flex items-center px-3 py-1.5 bg-white text-pln-blue rounded-md hover:bg-gray-100 transition-colors text-sm">
                        <i class="fas fa-download mr-2"></i>Unduh
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- PDF Viewer -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="border-b border-gray-200 px-5 py-4 flex justify-between items-center">
            <h2 class="text-lg font-medium text-gray-700 flex items-center">
                <i class="fas fa-file-pdf text-red-600 mr-2"></i>
                Pratinjau Dokumen
            </h2>
            <div class="flex items-center space-x-4">
                <div class="text-sm text-gray-500">
                    Diunggah: {{ \Carbon\Carbon::parse($dokumen->tanggal_upload)->format('d M Y') }}
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $dokumen->status === 'disetujui_atasan' ? 'bg-green-100 text-green-800' : ($dokumen->status === 'ditolak_atasan' || $dokumen->status === 'ditolak_manejer' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                    {{ $dokumen->status_label ?? ucfirst(str_replace('_', ' ', $dokumen->status)) }}
                </span>
            </div>
        </div>

        <!-- PDF Embed with responsive container -->
        <div class="w-full p-2 md:p-5">
            <div class="w-full bg-gray-100 rounded-lg relative" style="height: 80vh;">
                <iframe 
                    src="{{ $fileUrl }}#toolbar=1&navpanes=1&scrollbar=1" 
                    class="absolute inset-0 w-full h-full rounded-lg" 
                    frameborder="0"
                    title="PDF Document Viewer">
                </iframe>
            </div>
        </div>
    </div>

    <!-- Document Actions for Approval Process -->
    @if(in_array(Auth::user()->role, ['keuangan', 'manajer', 'atasan']))
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="p-5 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-700">Tindakan Dokumen</h2>
        </div>
        <div class="p-5 flex flex-wrap gap-3">
            @if(Auth::user()->role === 'keuangan' && $dokumen->status === 'dikirim')
                <form action="{{ route('dokumen.terimaKeuangan', $dokumen->id_dokumen) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors">
                        <i class="fas fa-check mr-2"></i>
                        Terima Dokumen
                    </button>
                </form>

                <form action="{{ route('dokumen.teruskanKeManajer', $dokumen->id_dokumen) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-share mr-2"></i>
                        Teruskan ke Manajer
                    </button>
                </form>
            @endif

            @if(Auth::user()->role === 'manajer' && $dokumen->status === 'diteruskan_ke_manejer')
                <form action="{{ route('dokumen.setujuiManajer', $dokumen->id_dokumen) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors">
                        <i class="fas fa-check mr-2"></i>
                        Setujui Dokumen
                    </button>
                </form>

                <button type="button" onclick="showTolakModal('manajer')" class="inline-flex items-center px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Tolak Dokumen
                </button>
            @endif

            @if(Auth::user()->role === 'manajer' && $dokumen->status === 'disetujui_manejer')
                <form action="{{ route('dokumen.teruskanKeAtasan', $dokumen->id_dokumen) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-share mr-2"></i>
                        Teruskan ke Atasan
                    </button>
                </form>
            @endif

            @if(Auth::user()->role === 'atasan' && $dokumen->status === 'diteruskan_ke_atasan')
                <form action="{{ route('dokumen.setujuiAtasan', $dokumen->id_dokumen) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors">
                        <i class="fas fa-check mr-2"></i>
                        Setujui Dokumen
                    </button>
                </form>

                <button type="button" onclick="showTolakModal('atasan')" class="inline-flex items-center px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Tolak Dokumen
                </button>
            @endif
        </div>
    </div>
    @endif
</div>

<!-- Rejection Modal for Manager -->
<div id="tolakManajerModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
        <div class="p-5 border-b">
            <h3 class="text-xl font-medium text-gray-900">Tolak Dokumen</h3>
        </div>
        <form action="{{ route('dokumen.tolakManajer', $dokumen->id_dokumen) }}" method="POST">
            @csrf
            <div class="p-5">
                <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan</label>
                <textarea id="catatan" name="catatan" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pln-blue" required></textarea>
            </div>
            <div class="p-4 bg-gray-50 flex justify-end space-x-3 rounded-b-lg">
                <button type="button" onclick="hideTolakModal('manajer')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors">
                    Tolak Dokumen
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Rejection Modal for Superior -->
<div id="tolakAtasanModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
        <div class="p-5 border-b">
            <h3 class="text-xl font-medium text-gray-900">Tolak Dokumen</h3>
        </div>
        <form action="{{ route('dokumen.tolakAtasan', $dokumen->id_dokumen) }}" method="POST">
            @csrf
            <div class="p-5">
                <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan</label>
                <textarea id="catatan" name="catatan" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pln-blue" required></textarea>
            </div>
            <div class="p-4 bg-gray-50 flex justify-end space-x-3 rounded-b-lg">
                <button type="button" onclick="hideTolakModal('atasan')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors">
                    Tolak Dokumen
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function showTolakModal(role) {
        document.getElementById('tolak' + role.charAt(0).toUpperCase() + role.slice(1) + 'Modal').classList.remove('hidden');
        document.getElementById('tolak' + role.charAt(0).toUpperCase() + role.slice(1) + 'Modal').classList.add('flex');
    }
    
    function hideTolakModal(role) {
        document.getElementById('tolak' + role.charAt(0).toUpperCase() + role.slice(1) + 'Modal').classList.add('hidden');
        document.getElementById('tolak' + role.charAt(0).toUpperCase() + role.slice(1) + 'Modal').classList.remove('flex');
    }

    // Add PDF viewer controls and zoom functionality if needed
    document.addEventListener('DOMContentLoaded', function() {
        // Any additional JavaScript for PDF viewer enhancements
    });
</script>
@endsection