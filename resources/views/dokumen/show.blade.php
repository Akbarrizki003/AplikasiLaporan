@extends('layouts.app')

@section('content')
<div class="bg-white rounded-xl shadow-md overflow-hidden">
    <!-- Header with document info -->
    <div class="bg-gradient-to-r from-pln-blue to-pln-blue-light p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between">
            <div>
                <h1 class="text-white text-2xl font-semibold mb-2">{{ $dokumen->nama_dokumen }}</h1>
                <div class="flex items-center text-white/80 text-sm">
                    <i class="fas fa-building mr-2"></i>
                    <span>{{ $dokumen->unit->nama_unit ?? 'Unit tidak ditemukan' }}</span>
                </div>
            </div>
            <div class="mt-4 md:mt-0">
                <span class="inline-block px-4 py-2 rounded-full {{ $dokumen->status === 'disetujui_atasan' ? 'bg-green-500' : ($dokumen->status === 'ditolak_atasan' || $dokumen->status === 'ditolak_manejer' ? 'bg-red-500' : 'bg-pln-yellow') }} text-white text-sm font-medium">
                    {{ $dokumen->status_label ?? ucfirst(str_replace('_', ' ', $dokumen->status)) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Document metadata -->
    <div class="p-6 border-b">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-gray-500 text-sm font-medium mb-1">Tanggal Upload</h3>
                <p class="text-gray-800">{{ \Carbon\Carbon::parse($dokumen->tanggal_upload)->format('d M Y') }}</p>
            </div>
            <div>
                <h3 class="text-gray-500 text-sm font-medium mb-1">ID Dokumen</h3>
                <p class="text-gray-800">#{{ $dokumen->id }}</p>
            </div>
        </div>
    </div>

    <!-- Document actions -->
    <div class="p-6 flex flex-wrap gap-3">
        <!-- View Document Button -->
        <a href="{{ route('dokumen.viewDokumen', $dokumen->id_dokumen) }}" class="inline-flex items-center px-4 py-2 bg-pln-blue text-white rounded-md hover:bg-pln-blue-dark transition-colors">
            <i class="fas fa-eye mr-2"></i>
            Lihat Dokumen
        </a>

        <!-- Download Button -->
        <a href="{{ Storage::disk('public')->url($dokumen->file) }}" download class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
            <i class="fas fa-download mr-2"></i>
            Download
        </a>

        @if(Auth::user()->role === 'unit' && in_array($dokumen->status, ['dikirim', 'ditolak_manejer', 'ditolak_atasan']))
            <!-- Edit Button - Only for unit user with specific document statuses -->
            <a href="{{ route('dokumen.edit', $dokumen->id_dokumen) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition-colors">
                <i class="fas fa-edit mr-2"></i>
                Edit
            </a>
        @endif

        @if(Auth::user()->role === 'keuangan' && $dokumen->status === 'dikirim')
            <!-- Finance department actions -->
            <form action="{{ route('dokumen.terimaKeuangan', $dokumen->id_dokumen) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors">
                    <i class="fas fa-check mr-2"></i>
                    Terima
                </button>
            </form>

            <form action="{{ route('dokumen.teruskanKeManajer', $dokumen->id_dokumen) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                    <i class="fas fa-share mr-2"></i>
                    Teruskan ke Manajer
                </button>
            </form>
        @endif

        @if(Auth::user()->role === 'manajer' && $dokumen->status === 'diteruskan_ke_manejer')
            <!-- Manager actions -->
            <form action="{{ route('dokumen.setujuiManajer', $dokumen->id_dokumen) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors">
                    <i class="fas fa-check mr-2"></i>
                    Setujui
                </button>
            </form>

            <button type="button" onclick="showTolakModal('manajer')" class="inline-flex items-center px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors">
                <i class="fas fa-times mr-2"></i>
                Tolak
            </button>
        @endif

        @if(Auth::user()->role === 'manajer' && $dokumen->status === 'disetujui_manejer')
            <!-- Forward to higher-up action -->
            <form action="{{ route('dokumen.teruskanKeAtasan', $dokumen->id_dokumen) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">
                    <i class="fas fa-share mr-2"></i>
                    Teruskan ke Atasan
                </button>
            </form>
        @endif

        @if(Auth::user()->role === 'atasan' && $dokumen->status === 'diteruskan_ke_atasan')
            <!-- Superior actions -->
            <form action="{{ route('dokumen.setujuiAtasan', $dokumen->id_dokumen) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors">
                    <i class="fas fa-check mr-2"></i>
                    Setujui
                </button>
            </form>

            <button type="button" onclick="showTolakModal('atasan')" class="inline-flex items-center px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors">
                <i class="fas fa-times mr-2"></i>
                Tolak
            </button>
        @endif
    </div>

    <!-- Rejection notes if available -->
    @if($dokumen->catatan && in_array($dokumen->status, ['ditolak_manejer', 'ditolak_atasan']))
        <div class="p-6 bg-red-50 border-t">
            <h3 class="text-red-700 font-medium mb-2 flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>Catatan Penolakan
            </h3>
            <div class="bg-white p-4 border border-red-200 rounded-md text-gray-700">
                {{ $dokumen->catatan }}
            </div>
        </div>
    @endif

    <!-- Document history - could be extended with a table of document status changes -->
    <div class="p-6 bg-gray-50 border-t">
        <h3 class="text-gray-700 font-medium mb-4">Histori Dokumen</h3>
        <div class="flex flex-col">
            <div class="flex items-center pb-4">
                <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center text-white">
                    <i class="fas fa-file-upload"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900">Dokumen diunggah</p>
                    <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($dokumen->tanggal_upload)->format('d M Y, H:i') }}</p>
                </div>
            </div>
            
            <!-- Additional history entries would be dynamically generated here based on document status -->
        </div>
    </div>
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
</script>
@endsection