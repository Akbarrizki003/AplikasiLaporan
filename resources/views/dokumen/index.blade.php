@extends('layouts.app')

@section('content')
<style>
.card {
    background-color: #007B7F; /* Warna latar belakang */
    color: white;              /* Warna teks */
    border-radius: 12px;       /* Sudut membulat */
    padding: 10px;             /* Jarak dalam diperbesar */
    text-align: center;        /* Teks rata tengah */
    width: 400px;              /* Lebar card diperbesar */
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2); /* Efek bayangan */
    font-family: Arial, sans-serif;
}

.time {
    font-size: 42px;           /* Ukuran font waktu diperbesar */
    font-weight: bold;
}

.date {
    font-size: 18px;           /* Ukuran font tanggal diperbesar */
    margin-top: 12px;
}
</style>
<div class="bg-slate-50 min-h-screen">
    <div class="container mx-auto px-4 py-6">
        <!-- Welcome Card -->
        <div class="bg-gray-100 border border-gray-300 rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Selamat Datang, {{ Auth::user()->name }}!</h2>
            <p class="text-gray-700 text-base mt-2">Pesan ini hanya untuk pengguna yang mengakses halaman ini.</p>
        </div>


        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Main Content Area (2/3) -->
            <div class="w-full">
            <!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 justify-end">
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
        <p class="text-gray-500 text-sm font-medium">Total Pengajuan</p>
        <h3 class="text-2xl font-bold text-gray-800">{{ $dokumen->count() }}</h3>
        <div class="flex items-center text-xs text-green-600 mt-2">
            <i class="bi bi-arrow-up me-1"></i>
            <span>Data terbaru</span>
        </div>
    </div>
    @php
        $approved = $dokumen->where('status', 'disetujui_atasan')->count();
        $percentage = $dokumen->count() > 0 ? round(($approved / $dokumen->count()) * 100) : 0;
    @endphp
    <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
        <p class="text-gray-500 text-sm font-medium">Dokumen Disetujui</p>
        <h3 class="text-2xl font-bold text-gray-800">{{ $approved }}</h3>
        <div class="flex items-center text-xs text-green-600 mt-2">
            <i class="bi bi-check-circle me-1"></i>
            <span>{{ $percentage }}% tingkat persetujuan</span>
        </div>
    </div>
    <div class="card mb-6"> <!-- Menggunakan kelas .card yang telah Anda buat -->
        <div class="time">14.20</div>
        <div class="date">02 Mei 2025</div>
    </div>
</div>


                <!-- Removed w-full class since it's now in a container that's already full width -->
                <div class="bg-white rounded-lg shadow-sm mb-6">
                    <div class="p-4 border-b border-gray-100 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-800">Data Laporan Pengajuan</h2>
                        <div class="flex space-x-2">
                            @if(Auth::user()->role === 'unit')
                                <a href="{{ route('dokumen.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm flex items-center">
                                    <i class="bi bi-plus me-1"></i> Tambah
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Table Tabs -->
                    <div class="flex border-b">
                        <button class="px-4 py-2 text-sm font-medium text-blue-600 border-b-2 border-blue-500">Pengajuan</button>
                        <button class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">Proses</button>
                        <button class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">Riwayat Selesai</button>
                    </div>

                    <!-- Search and Filter -->
                    <div class="p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="relative">
                            <input type="text" placeholder="Cari dokumen..." class="w-full sm:w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="bi bi-search text-gray-400"></i>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <select class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua Status</option>
                                <option value="dikirim">Dikirim</option>
                                <option value="diterima_keuangan">Diterima Keuangan</option>
                                <option value="diteruskan_ke_manejer">Diteruskan ke Manajer</option>
                                <option value="disetujui_manejer">Disetujui Manajer</option>
                                <option value="ditolak_manejer">Ditolak Manajer</option>
                                <option value="diteruskan_ke_atasan">Diteruskan ke Atasan</option>
                                <option value="disetujui_atasan">Disetujui Atasan</option>
                                <option value="ditolak_atasan">Ditolak Atasan</option>
                            </select>
                            <button class="bg-gray-100 hover:bg-gray-200 px-3 py-2 rounded-md">
                                <i class="bi bi-filter"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Dokumen</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pengajuan</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($dokumen as $key => $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $dokumen->firstItem() + $key }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->nama_dokumen }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($item->tanggal_upload)->format('d M Y') }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @switch($item->status)
                                                @case('dikirim')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-primary-100 text-primary-800">Dikirim</span>
                                                    @break
                                                @case('diterima_keuangan')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Diterima Keuangan</span>
                                                    @break
                                                @case('diteruskan_ke_manejer')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Diteruskan ke Manajer</span>
                                                    @break
                                                @case('disetujui_manejer')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Disetujui Manajer</span>
                                                    @break
                                                @case('ditolak_manejer')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Ditolak Manajer</span>
                                                    @break
                                                @case('diteruskan_ke_atasan')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">Diteruskan ke Atasan</span>
                                                    @break
                                                @case('disetujui_atasan')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-800">Disetujui Atasan</span>
                                                    @break
                                                @case('ditolak_atasan')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Ditolak Atasan</span>
                                                    @break
                                                @default
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">{{ $item->status }}</span>
                                            @endswitch
                                            @if($item->catatan && in_array($item->status, ['ditolak_manejer', 'ditolak_atasan']))
                                                <a href="#" data-bs-toggle="tooltip" data-tooltip-target="tooltip-{{ $item->id_dokumen }}" title="{{ $item->catatan }}">
                                                    <i class="bi bi-info-circle ml-1 text-gray-500"></i>
                                                </a>
                                                <div id="tooltip-{{ $item->id_dokumen }}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip">
                                                    {{ $item->catatan }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-2">
                                                <!-- Aksi untuk semua role -->
                                                <a href="{{ route('dokumen.show', $item->id_dokumen) }}" class="text-blue-600 hover:text-blue-900" title="Lihat">
                                                    <i class="bi bi-eye"></i>
                                                </a>

                                                @if(Auth::user()->role === 'unit' && Auth::user()->id_unit === $item->id_unit)
                                                    <!-- Aksi khusus unit -->
                                                    <a href="{{ route('dokumen.download', $item->id_dokumen) }}" class="text-green-600 hover:text-green-900" title="Download">
                                                        <i class="bi bi-download"></i>
                                                    </a>

                                                    @if(in_array($item->status, ['dikirim', 'ditolak_manejer', 'ditolak_atasan']))
                                                        <a href="{{ route('dokumen.edit', $item->id_dokumen) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        
                                                        <form action="{{ route('dokumen.destroy', $item->id_dokumen) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus" 
                                                                onclick="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?')">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif

                                                @if(Auth::user()->role === 'keuangan')
                                                    <!-- Aksi khusus keuangan -->
                                                    @if($item->status === 'dikirim')
                                                        <form action="{{ route('dokumen.terimaKeuangan', $item->id_dokumen) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="text-green-600 hover:text-green-900" title="Terima">
                                                                <i class="bi bi-check-circle"></i>
                                                            </button>
                                                        </form>
                                                    @endif

                                                    @if($item->status === 'diterima_keuangan')
                                                        <form action="{{ route('dokumen.teruskanKeManajer', $item->id_dokumen) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="text-blue-600 hover:text-blue-900" title="Teruskan ke Manajer">
                                                                <i class="bi bi-forward"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif

                                                @if(Auth::user()->role === 'manajer')
                                                    <!-- Aksi khusus manajer -->
                                                    @if($item->status === 'diteruskan_ke_manejer')
                                                        <form action="{{ route('dokumen.setujuiManajer', $item->id_dokumen) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="text-green-600 hover:text-green-900" title="Setujui">
                                                                <i class="bi bi-check-circle"></i>
                                                            </button>
                                                        </form>

                                                        <button type="button" class="text-red-600 hover:text-red-900" title="Tolak" 
                                                            data-bs-toggle="modal" data-bs-target="#tolakModal-{{ $item->id_dokumen }}">
                                                            <i class="bi bi-x-circle"></i>
                                                        </button>
                                                    @endif

                                                    @if($item->status === 'disetujui_manejer')
                                                        <form action="{{ route('dokumen.teruskanKeAtasan', $item->id_dokumen) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="text-blue-600 hover:text-blue-900" title="Teruskan ke Atasan">
                                                                <i class="bi bi-forward"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endif

                                                @if(Auth::user()->role === 'atasan')
                                                    <!-- Aksi khusus atasan -->
                                                    @if($item->status === 'diteruskan_ke_atasan')
                                                        <form action="{{ route('dokumen.setujuiAtasan', $item->id_dokumen) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="text-green-600 hover:text-green-900" title="Setujui">
                                                                <i class="bi bi-check-circle"></i>
                                                            </button>
                                                        </form>

                                                        <button type="button" class="text-red-600 hover:text-red-900" title="Tolak" 
                                                            data-bs-toggle="modal" data-bs-target="#tolakModal-{{ $item->id_dokumen }}">
                                                            <i class="bi bi-x-circle"></i>
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Modal Tolak untuk Manajer dan Atasan -->
                                    @if((Auth::user()->role === 'manajer' && $item->status === 'diteruskan_ke_manejer') || 
                                        (Auth::user()->role === 'atasan' && $item->status === 'diteruskan_ke_atasan'))
                                        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" id="tolakModal-{{ $item->id_dokumen }}">
                                            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                                                <div class="flex justify-between items-center mb-4">
                                                    <h3 class="text-lg font-medium text-gray-900">Tolak Dokumen</h3>
                                                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="document.getElementById('tolakModal-{{ $item->id_dokumen }}').classList.add('hidden')">
                                                        <span class="sr-only">Close</span>
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                </div>
                                                <form action="{{ Auth::user()->role === 'manajer' ? route('dokumen.tolakManajer', $item->id_dokumen) : route('dokumen.tolakAtasan', $item->id_dokumen) }}" method="POST">
                                                    @csrf
                                                    <div class="mb-4">
                                                        <label for="catatan" class="block text-sm font-medium text-gray-700 mb-1">Catatan Penolakan</label>
                                                        <textarea class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" 
                                                            id="catatan" name="catatan" rows="3" required></textarea>
                                                    </div>
                                                    <div class="flex justify-end space-x-3">
                                                        <button type="button" class="bg-gray-100 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-200"
                                                            onclick="document.getElementById('tolakModal-{{ $item->id_dokumen }}').classList.add('hidden')">
                                                            Batal
                                                        </button>
                                                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                                                            Tolak Dokumen
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500">Tidak ada dokumen ditemukan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="px-4 py-3 flex items-center justify-between border-t border-gray-200">
                        <div class="flex-1 flex justify-between sm:hidden">
                            @if($dokumen->onFirstPage())
                                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-md">
                                    Previous
                                </span>
                            @else
                                <a href="{{ $dokumen->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                                    Previous
                                </a>
                            @endif

                            @if($dokumen->hasMorePages())
                                <a href="{{ $dokumen->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                                    Next
                                </a>
                            @else
                                <span class="ml-3 relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-default rounded-md">
                                    Next
                                </span>
                            @endif
                        </div>

                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700 leading-5">
                                    Menampilkan
                                    <span class="font-medium">{{ $dokumen->firstItem() }}</span>
                                    sampai
                                    <span class="font-medium">{{ $dokumen->lastItem() }}</span>
                                    dari
                                    <span class="font-medium">{{ $dokumen->total() }}</span>
                                    hasil
                                </p>
                            </div>

                            <div>
                                {{ $dokumen->links('pagination::tailwind') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>

<!-- Notification Modal -->
<div id="notificationModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="flex justify-between items-center p-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Notifikasi</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="p-4 max-h-96 overflow-y-auto">
            <div class="space-y-4">
                <div class="flex items-start p-3 bg-blue-50 rounded-lg">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                        <i class="bi bi-envelope text-blue-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Dokumen baru memerlukan persetujuan</p>
                        <p class="text-xs text-gray-600 mt-1">5 menit yang lalu</p>
                    </div>
                </div>
                <div class="flex items-start p-3 bg-green-50 rounded-lg">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                        <i class="bi bi-check-circle text-green-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Dokumen "Laporan Keuangan Q1" telah disetujui</p>
                        <p class="text-xs text-gray-600 mt-1">2 jam yang lalu</p>
                    </div>
                </div>
                <div class="flex items-start p-3 bg-yellow-50 rounded-lg">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center">
                        <i class="bi bi-clock text-yellow-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Pengingat: Batas waktu pengajuan dokumen</p>
                        <p class="text-xs text-gray-600 mt-1">1 hari yang lalu</p>
                    </div>
                </div>
                <div class="flex items-start p-3 rounded-lg">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                        <i class="bi bi-bell text-gray-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">Sistem akan maintenance pada tanggal 30 April 2025</p>
                        <p class="text-xs text-gray-600 mt-1">3 hari yang lalu</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4 border-t border-gray-200 text-right">
            <button class="text-sm text-blue-600 hover:text-blue-800 font-medium">Tandai semua telah dibaca</button>
        </div>
    </div>
</div>

<script>
// Modal functionality
function openModal() {
    document.getElementById('notificationModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('notificationModal').classList.add('hidden');
}

// Close modal when clicking outside
window.onclick = function(event) {
    let modal = document.getElementById('notificationModal');
    if (event.target == modal) {
        closeModal();
    }
}

// Initialize tooltips if using Bootstrap
document.addEventListener('DOMContentLoaded', function() {
    // Add any initialization code here
    console.log('Dashboard fully loaded');
    
    // Toggle mobile menu
    let menuButton = document.querySelector('.fa-bars').parentElement;
    menuButton.addEventListener('click', function() {
        let mobileMenu = document.querySelector('nav');
        mobileMenu.classList.toggle('hidden');
    });
    
    // Sample tab functionality
    let tabs = document.querySelectorAll('.flex.border-b button');
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active class from all tabs
            tabs.forEach(t => {
                t.classList.remove('text-blue-600', 'border-b-2', 'border-blue-500');
                t.classList.add('text-gray-500');
            });
            
            // Add active class to clicked tab
            this.classList.remove('text-gray-500');
            this.classList.add('text-blue-600', 'border-b-2', 'border-blue-500');
        });
    });
});
</script>
@endsection