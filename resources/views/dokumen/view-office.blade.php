@extends('layouts.app')

@section('content')
<div class="bg-white rounded-xl shadow-md overflow-hidden">
    <!-- Header -->
    <div class="bg-pln-blue px-6 py-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between">
            <h2 class="text-xl font-semibold text-white">
                <i class="fas fa-file-alt mr-2"></i>{{ $dokumen->nama_dokumen }}
            </h2>
            <div class="flex items-center mt-3 md:mt-0 space-x-3">
                <span class="bg-white text-pln-blue text-sm px-3 py-1 rounded-full font-medium">
                    {{ ucfirst(str_replace('_', ' ', $dokumen->status)) }}
                </span>
                <a href="{{ route('dokumen.index') }}" class="text-white hover:text-pln-yellow transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Document Info -->
    <div class="px-6 py-4 bg-gray-50 border-b">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-gray-600">
                    <span class="font-medium text-pln-blue-dark">Unit:</span> 
                    {{ $dokumen->unit->nama_unit ?? 'N/A' }}
                </p>
                <p class="text-gray-600">
                    <span class="font-medium text-pln-blue-dark">Tanggal Upload:</span> 
                    {{ \Carbon\Carbon::parse($dokumen->tanggal_upload)->format('d F Y') }}
                </p>
            </div>
            <div>
                <p class="text-gray-600">
                    <span class="font-medium text-pln-blue-dark">Tipe File:</span> 
                    <span class="uppercase">{{ $fileExtension }}</span>
                </p>
                <p class="text-gray-600">
                    <span class="font-medium text-pln-blue-dark">Status:</span> 
                    <span class="capitalize">{{ str_replace('_', ' ', $dokumen->status) }}</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Document Actions -->
    <div class="px-6 py-3 bg-gray-100 border-b flex flex-wrap gap-2">
        <a href="{{ Storage::disk('public')->url($dokumen->file) }}" class="bg-pln-blue hover:bg-pln-blue-dark text-white px-4 py-2 rounded-lg transition-colors text-sm flex items-center" download>
            <i class="fas fa-download mr-1.5"></i>Download
        </a>
        
        <a href="{{ route('dokumen.show', $dokumen->id) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors text-sm flex items-center">
            <i class="fas fa-info-circle mr-1.5"></i>Detail
        </a>
    </div>

    <!-- Document Viewer -->
    <div class="p-6">
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <!-- Office Viewer Header -->
            <div class="bg-gray-100 text-pln-blue-dark py-2 px-4 border-b flex items-center">
                <i class="
                    {{ $fileExtension == 'doc' || $fileExtension == 'docx' ? 'fas fa-file-word text-blue-600' : '' }}
                    {{ $fileExtension == 'xls' || $fileExtension == 'xlsx' ? 'fas fa-file-excel text-green-600' : '' }}
                    mr-2 text-lg"></i>
                <span class="font-medium">{{ $dokumen->nama_dokumen }}</span>
            </div>
            
            <!-- Office Viewer -->
            <div class="flex justify-center items-center p-4">
                @if($fileExtension == 'doc' || $fileExtension == 'docx')
                    <div class="w-full h-96 md:h-[36rem] bg-gray-50 rounded-lg overflow-hidden">
                        <iframe src="https://view.officeapps.live.com/op/embed.aspx?src={{ urlencode(url($fileUrl)) }}" 
                                class="w-full h-full border-0"></iframe>
                    </div>
                @elseif($fileExtension == 'xls' || $fileExtension == 'xlsx')
                    <div class="w-full h-96 md:h-[36rem] bg-gray-50 rounded-lg overflow-hidden">
                        <iframe src="https://view.officeapps.live.com/op/embed.aspx?src={{ urlencode(url($fileUrl)) }}" 
                                class="w-full h-full border-0"></iframe>
                    </div>
                @else
                    <div class="text-center py-12 px-4">
                        <i class="fas fa-file-alt text-gray-400 text-5xl mb-4"></i>
                        <p class="text-gray-600">Format file tidak dapat ditampilkan langsung.</p>
                        <a href="{{ Storage::disk('public')->url($dokumen->file) }}" class="text-pln-blue hover:text-pln-blue-dark mt-2 inline-block font-medium" download>
                            Download file
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Notes Section -->
    @if($dokumen->catatan)
    <div class="px-6 py-4 bg-yellow-50 border-t">
        <h3 class="text-lg font-medium text-gray-800 mb-2">
            <i class="fas fa-sticky-note text-yellow-500 mr-2"></i>Catatan
        </h3>
        <div class="bg-white p-4 rounded-lg border border-yellow-200">
            <p class="text-gray-700">{{ $dokumen->catatan }}</p>
        </div>
    </div>
    @endif
</div>

<!-- Responsive Footer -->
<div class="mt-8 text-center text-gray-500 text-sm">
    <p>© {{ date('Y') }} PLN - Sistem Pengajuan Laporan</p>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Add any JavaScript functionality you need here
    });
</script>
@endpush