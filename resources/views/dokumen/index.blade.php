
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
            <p class="text-gray-700 text-base mt-2">Di Aplikasi Pengajuan Laporan</p>
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
                <div class="card mb-6" id="timeCard"> <!-- Menggunakan kelas .card yang telah Anda buat -->
                    <div class="time" id="currentTime"></div>
                    <div class="date" id="currentDate"></div>
                </div>
                <script>
    window.onload = function () {
        function updateTimeAndDate() {
            const now = new Date();

            const time = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });

            const date = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            document.getElementById('currentTime').textContent = time;
            document.getElementById('currentDate').textContent = date;
        }

        updateTimeAndDate();
        setInterval(updateTimeAndDate, 1000);
    };
</script>
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

    <!-- Status Filter Tabs -->
    <div class="flex border-b">
        <a href="{{ route('dokumen.index', ['status_group' => 'pengajuan']) }}" class="px-4 py-2 text-sm font-medium {{ request('status_group') == 'pengajuan' || !request('status_group') ? 'text-blue-600 border-b-2 border-blue-500' : 'text-gray-500 hover:text-gray-700' }}">
            Pengajuan
        </a>
        <a href="{{ route('dokumen.index', ['status_group' => 'proses']) }}" class="px-4 py-2 text-sm font-medium {{ request('status_group') == 'proses' ? 'text-blue-600 border-b-2 border-blue-500' : 'text-gray-500 hover:text-gray-700' }}">
            Proses
        </a>
        <a href="{{ route('dokumen.index', ['status_group' => 'selesai']) }}" class="px-4 py-2 text-sm font-medium {{ request('status_group') == 'selesai' ? 'text-blue-600 border-b-2 border-blue-500' : 'text-gray-500 hover:text-gray-700' }}">
            Riwayat Selesai
        </a>
    </div>

    <!-- Search and Filter -->
    <div class="p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <form action="{{ route('dokumen.index') }}" method="GET" class="flex flex-col sm:flex-row sm:items-center gap-3 w-full">
            <input type="hidden" name="status_group" value="{{ request('status_group', 'pengajuan') }}">
            <div class="relative flex-grow">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari dokumen..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="bi bi-search text-gray-400"></i>
                </div>
            </div>
            <div class="flex gap-2">
                <select name="status" class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="dikirim" {{ request('status') == 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                    <option value="diterima_keuangan" {{ request('status') == 'diterima_keuangan' ? 'selected' : '' }}>Diterima Keuangan</option>
                    <option value="diteruskan_ke_manejer" {{ request('status') == 'diteruskan_ke_manejer' ? 'selected' : '' }}>Diteruskan ke Manajer</option>
                    <option value="disetujui_manejer" {{ request('status') == 'disetujui_manejer' ? 'selected' : '' }}>Disetujui Manajer</option>
                    <option value="ditolak_manejer" {{ request('status') == 'ditolak_manejer' ? 'selected' : '' }}>Ditolak Manajer</option>
                    <option value="diteruskan_ke_atasan" {{ request('status') == 'diteruskan_ke_atasan' ? 'selected' : '' }}>Diteruskan ke Atasan</option>
                    <option value="disetujui_atasan" {{ request('status') == 'disetujui_atasan' ? 'selected' : '' }}>Disetujui Atasan</option>
                    <option value="ditolak_atasan" {{ request('status') == 'ditolak_atasan' ? 'selected' : '' }}>Ditolak Atasan</option>
                </select>
                <button type="submit" class="bg-gray-100 hover:bg-gray-200 px-3 py-2 rounded-md">
                    <i class="bi bi-filter"></i>
                </button>
            </div>
        </form>
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
                                <span class="relative">
                                    <i class="bi bi-info-circle ml-1 text-gray-500 cursor-help tooltip-trigger" data-tooltip-id="tooltip-{{ $item->id_dokumen }}"></i>
                                    <div id="tooltip-{{ $item->id_dokumen }}" class="tooltip-content hidden absolute z-10 p-2 bg-gray-900 text-white text-sm rounded shadow-lg w-64 mt-1 right-0">
                                        {{ $item->catatan }}
                                    </div>
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <!-- Aksi untuk semua role -->
                                <a href="{{ route('dokumen.show', $item->id_dokumen) }}" class="text-blue-600 hover:text-blue-900" title="Lihat">
                                    <i class="bi bi-eye"></i>
                                </a>

                                @if(Auth::user()->role === 'unit' && $item->id_unit === Auth::user()->unit->id_unit)
                                    <!-- Aksi khusus unit -->
                                    <a href="{{ route('dokumen.viewDokumen', $item->id_dokumen) }}" class="text-green-600 hover:text-green-900" title="Lihat File">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </a>

                                    @if(in_array($item->status, ['dikirim', 'ditolak_manejer', 'ditolak_atasan']))
                                        <a href="{{ route('dokumen.edit', $item->id_dokumen) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        
                                        <form action="{{ route('dokumen.destroy', $item->id_dokumen) }}" method="POST" class="inline">
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
                                    <a href="{{ route('dokumen.viewDokumen', $item->id_dokumen) }}" class="text-green-600 hover:text-green-900" title="Lihat File">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </a>
                                    
                                    @if($item->status === 'dikirim')
                                        <form action="{{ route('dokumen.terimaKeuangan', $item->id_dokumen) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900" title="Terima">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if($item->status === 'diterima_keuangan')
                                        <form action="{{ route('dokumen.teruskanKeManajer', $item->id_dokumen) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-blue-600 hover:text-blue-900" title="Teruskan ke Manajer">
                                                <i class="bi bi-forward"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endif

                                @if(Auth::user()->role === 'manajer')
                                    <!-- Aksi khusus manajer -->
                                    <a href="{{ route('dokumen.viewDokumen', $item->id_dokumen) }}" class="text-green-600 hover:text-green-900" title="Lihat File">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </a>
                                    
                                    @if($item->status === 'diteruskan_ke_manejer')
                                        <form action="{{ route('dokumen.setujuiManajer', $item->id_dokumen) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900" title="Setujui">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        </form>

                                        <button type="button" class="text-red-600 hover:text-red-900 tolak-button" title="Tolak" 
                                            data-dokumen-id="{{ $item->id_dokumen }}" data-role="manajer">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif

                                    @if($item->status === 'disetujui_manejer')
                                        <form action="{{ route('dokumen.teruskanKeAtasan', $item->id_dokumen) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-blue-600 hover:text-blue-900" title="Teruskan ke Atasan">
                                                <i class="bi bi-forward"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endif

                                @if(Auth::user()->role === 'atasan')
                                    <!-- Aksi khusus atasan -->
                                    <a href="{{ route('dokumen.viewDokumen', $item->id_dokumen) }}" class="text-green-600 hover:text-green-900" title="Lihat File">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </a>
                                    
                                    @if($item->status === 'diteruskan_ke_atasan')
                                        <form action="{{ route('dokumen.setujuiAtasan', $item->id_dokumen) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900" title="Setujui">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        </form>

                                        <button type="button" class="text-red-600 hover:text-red-900 tolak-button" title="Tolak" 
                                            data-dokumen-id="{{ $item->id_dokumen }}" data-role="atasan">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
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
                <a href="{{ $dokumen->appends(request()->except('page'))->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
                    Previous
                </a>
            @endif

            @if($dokumen->hasMorePages())
                <a href="{{ $dokumen->appends(request()->except('page'))->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 leading-5 rounded-md hover:text-gray-500 focus:outline-none focus:ring focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150">
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
                    <span class="font-medium">{{ $dokumen->firstItem() ? $dokumen->firstItem() : 0 }}</span>
                    sampai
                    <span class="font-medium">{{ $dokumen->lastItem() ? $dokumen->lastItem() : 0 }}</span>
                    dari
                    <span class="font-medium">{{ $dokumen->total() }}</span>
                    hasil
                </p>
            </div>

            <div>
                {{ $dokumen->appends(request()->except('page'))->links('pagination::tailwind') }}
            </div>
        </div>
    </div>
</div>

<!-- Modal for rejection comments -->
<div id="tolak-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-semibold mb-4">Tolak Dokumen</h3>
        
        <form id="tolak-form" method="POST">
            @csrf
            <div class="mb-4">
                <label for="catatan" class="block text-sm font-medium text-gray-700 mb-1">Catatan Penolakan</label>
                <textarea id="catatan" name="catatan" rows="4" class="w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-blue-500 focus:border-blue-500" required></textarea>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" id="close-tolak-modal" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-md text-gray-800">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 rounded-md text-white">
                    Tolak Dokumen
                </button>
            </div>
        </form>
    </div>
</div>


            </div>
        </div>
    </div>
</div>

<!-- Generic Modal for Tolak Action (Both Manajer and Atasan) -->
<div id="tolakModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Tolak Dokumen</h3>
            <button type="button" class="text-gray-400 hover:text-gray-500" id="closeTolakModal">
                <span class="sr-only">Close</span>
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form id="tolakForm" method="POST">
            @csrf
            <div class="mb-4">
                <label for="catatan" class="block text-sm font-medium text-gray-700 mb-1">Catatan Penolakan</label>
                <textarea class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" 
                    id="catatan" name="catatan" rows="3" required></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" class="bg-gray-100 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-200" id="cancelTolakButton">
                    Batal
                </button>
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                    Tolak Dokumen
                </button>
            </div>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update date and time
    function updateDateTime() {
        const now = new Date();
        
        // Format time: HH.MM
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const timeString = `${hours}.${minutes}`;
        
        // Format date: DD Bulan YYYY
        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        const day = String(now.getDate()).padStart(2, '0');
        const month = months[now.getMonth()];
        const year = now.getFullYear();
        const dateString = `${day} ${month} ${year}`;
        
        // Update DOM
        const timeElement = document.getElementById('currentTime');
        const dateElement = document.getElementById('currentDate');
        
        if (timeElement) timeElement.textContent = timeString;
        if (dateElement) dateElement.textContent = dateString;
    }

    // Modal functionality
    function openModal() {
        document.getElementById('notificationModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('notificationModal').classList.add('hidden');
    }

    // Tolak Modal functionality
    function openTolakModal(dokumenId, role) {
        const modal = document.getElementById('tolakModal');
        const form = document.getElementById('tolakForm');
        
        // Set the form action based on role
        const route = role === 'manajer' 
            ? `{{ url('dokumen/tolak-manajer') }}/${dokumenId}` 
            : `{{ url('dokumen/tolak-atasan') }}/${dokumenId}`;
        
        form.action = route;
        modal.classList.remove('hidden');
    }

    function closeTolakModal() {
        document.getElementById('tolakModal').classList.add('hidden');
    }

    // Tooltip functionality
    function setupTooltips() {
        const tooltipTriggers = document.querySelectorAll('.tooltip-trigger');
        
        tooltipTriggers.forEach(trigger => {
            trigger.addEventListener('mouseenter', function() {
                const content = this.getAttribute('data-tooltip-content');
                const tooltip = this.nextElementSibling;
                
                tooltip.textContent = content;
                tooltip.classList.remove('hidden');
                
                // Position tooltip
                const triggerRect = this.getBoundingClientRect();
                tooltip.style.top = `${triggerRect.bottom + 5}px`;
                tooltip.style.left = `${triggerRect.left - 100}px`;
            });
            
            trigger.addEventListener('mouseleave', function() {
                this.nextElementSibling.classList.add('hidden');
            });
        });
    }

    // Tab functionality
    function setupTabs() {
        const tabs = document.querySelectorAll('.flex.border-b button');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs
                tabs.forEach(t => {
                    t.classList.remove('text-blue-600', 'border-b-2', 'border-blue-500');
                    t.classList.add('text-gray-500', 'hover:text-gray-700');
                });
                
                // Add active class to current tab
                this.classList.remove('text-gray-500', 'hover:text-gray-700');
                this.classList.add('text-blue-600', 'border-b-2', 'border-blue-500');
                
                // Get the tab id and filter table accordingly
                const tabId = this.id;
                filterTableByTab(tabId);
            });
        });
    }

    // Filter table based on selected tab
    function filterTableByTab(tabId) {
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const statusElement = row.querySelector('td:nth-child(4) span');
            if (!statusElement) return;
            
            const status = statusElement.textContent.trim().toLowerCase();
            
            switch(tabId) {
                case 'tab-pengajuan':
                    // Show rows with status dikirim, diterima_keuangan
                    row.classList.toggle('hidden', 
                        !(status.includes('dikirim') || status.includes('diterima keuangan')));
                    break;
                case 'tab-proses':
                    // Show rows with status diteruskan or disetujui (except disetujui_atasan)
                    row.classList.toggle('hidden', 
                        !(status.includes('diteruskan') || 
                          (status.includes('disetujui') && !status.includes('disetujui atasan'))));
                    break;
                case 'tab-selesai':
                    // Show rows with status disetujui_atasan, ditolak_manejer, ditolak_atasan
                    row.classList.toggle('hidden', 
                        !(status.includes('disetujui atasan') || status.includes('ditolak')));
                    break;
                default:
                    row.classList.remove('hidden');
            }
        });
    }

    // Search functionality
    function setupSearch() {
        const searchInput = document.getElementById('search-input');
        if (!searchInput) return;
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const docNameCell = row.querySelector('td:nth-child(2)');
                if (!docNameCell) return;
                
                const docName = docNameCell.textContent.toLowerCase();
                const isVisible = docName.includes(searchTerm);
                
                // Only hide if it's not already hidden by tab filter
                if (!row.classList.contains('hidden-by-tab')) {
                    row.classList.toggle('hidden-by-search', !isVisible);
                    row.classList.toggle('hidden', !isVisible);
                }
            });
        });
    }

    // Status filter functionality
    function setupStatusFilter() {
        const statusFilter = document.getElementById('status-filter');
        const filterButton = document.getElementById('filter-button');
        
        if (!statusFilter || !filterButton) return;
        
        filterButton.addEventListener('click', function() {
            const selectedStatus = statusFilter.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                if (selectedStatus === '') {
                    // If no filter, remove hidden-by-status class
                    row.classList.remove('hidden-by-status');
                    // Only show if not hidden by other filters
                    row.classList.toggle('hidden', 
                        row.classList.contains('hidden-by-tab') || 
                        row.classList.contains('hidden-by-search'));
                } else {
                    const statusCell = row.querySelector('td:nth-child(4) span');
                    const rowStatus = statusCell ? statusCell.textContent.trim().toLowerCase() : '';
                    const statusMatch = rowStatus.includes(selectedStatus.replace('_', ' '));
                    
                    row.classList.toggle('hidden-by-status', !statusMatch);
                    row.classList.toggle('hidden', 
                        !statusMatch || 
                        row.classList.contains('hidden-by-tab') || 
                        row.classList.contains('hidden-by-search'));
                }
            });
        });
    }

    // Initialize functionality
    // Set up the time display and update every second
    updateDateTime();
    setInterval(updateDateTime, 60000); // Update every minute
    
    // Set up tooltips
    setupTooltips();
    
    // Set up tabs
    setupTabs();
    
    // Set default tab (Pengajuan)
    const defaultTab = document.getElementById('tab-pengajuan');
    if (defaultTab) defaultTab.click();
    
    // Set up search functionality
    setupSearch();
    
    // Set up status filter
    setupStatusFilter();
    
    // Setup tolak buttons
    const tolakButtons = document.querySelectorAll('.tolak-button');
    tolakButtons.forEach(button => {
        button.addEventListener('click', function() {
            const dokumenId = this.getAttribute('data-dokumen-id');
            const role = this.getAttribute('data-role');
            openTolakModal(dokumenId, role);
        });
    });
    
    // Setup modal close buttons
    const closeTolakModalButton = document.getElementById('closeTolakModal');
    const cancelTolakButton = document.getElementById('cancelTolakButton');
    
    if (closeTolakModalButton) {
        closeTolakModalButton.addEventListener('click', closeTolakModal);
    }
    
    if (cancelTolakButton) {
        cancelTolakButton.addEventListener('click', closeTolakModal);
    }
    
    // Setup notification button
    const notificationButton = document.querySelector('.notification-button');
    if (notificationButton) {
        notificationButton.addEventListener('click', openModal);
    }

    // Handle form submission for tolak action
    const tolakForm = document.getElementById('tolakForm');
    if (tolakForm) {
        tolakForm.addEventListener('submit', function(e) {
            // You can add validation here if needed
            // e.g., check if catatan is not empty
            const catatan = document.getElementById('catatan').value.trim();
            if (!catatan) {
                e.preventDefault();
                alert('Harap isi catatan penolakan');
            }
        });
    }
});
<!-- JavaScript for tooltip and modal functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab functionality
    // Note: We've replaced this with direct link handling above
    
    // Tooltip functionality
    const tooltipTriggers = document.querySelectorAll('.tooltip-trigger');
    tooltipTriggers.forEach(trigger => {
        const tooltipId = trigger.getAttribute('data-tooltip-id');
        const tooltipContent = document.getElementById(tooltipId);
        
        trigger.addEventListener('mouseenter', () => {
            tooltipContent.classList.remove('hidden');
        });
        
        trigger.addEventListener('mouseleave', () => {
            tooltipContent.classList.add('hidden');
        });
    });
    
    // Rejection modal functionality
    const tolakButtons = document.querySelectorAll('.tolak-button');
    const tolakModal = document.getElementById('tolak-modal');
    const closeTolakModal = document.getElementById('close-tolak-modal');
    const tolakForm = document.getElementById('tolak-form');
    
    tolakButtons.forEach(button => {
        button.addEventListener('click', () => {
            const dokumenId = button.getAttribute('data-dokumen-id');
            const role = button.getAttribute('data-role');
            
            // Set the form action based on the role
            if (role === 'manajer') {
                tolakForm.action = `/dokumen/tolak-manajer/${dokumenId}`;
            } else if (role === 'atasan') {
                tolakForm.action = `/dokumen/tolak-atasan/${dokumenId}`;
            }
            
            tolakModal.classList.remove('hidden');
        });
    });
    
    if (closeTolakModal) {
        closeTolakModal.addEventListener('click', () => {
            tolakModal.classList.add('hidden');
        });
    }
    
    // Close modal when clicking outside
    window.addEventListener('click', (event) => {
        if (event.target === tolakModal) {
            tolakModal.classList.add('hidden');
        }
    });
});
</script>
@endsection