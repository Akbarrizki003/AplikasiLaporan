@extends('layouts.app')

@section('content')
<div class="flex flex-col md:flex-row w-full gap-4">
    <!-- Left Side - User Unit Information -->
    <div class="w-full md:w-1/2 bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="bg-gradient-to-r from-[#0a4a5a] to-[#116072] text-white rounded-t-lg p-3 flex justify-between items-center">
            <h2 class="text-lg font-semibold">Informasi Unit</h2>
            @if(auth()->user()->role === 'unit')
                <a href="{{ route('unit.profile') }}" class="bg-white/10 hover:bg-white/20 text-white px-3 py-1 rounded text-sm font-medium transition flex items-center gap-1">
                    <i class="fas fa-edit text-xs"></i> Update Profil
                </a>
            @endif
        </div>
        
        <div class="p-4">
            <!-- Unit Logo & Basic Info -->
            <div class="flex items-center space-x-4 mb-4">
                @if(auth()->user()->unit && auth()->user()->unit->logo)
                    <img src="{{ asset('storage/' . auth()->user()->unit->logo) }}" 
                         alt="Logo Unit" 
                         class="h-16 w-16 object-contain rounded-full bg-gray-50 p-1 border border-gray-200">
                @else
                    <div class="h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center border border-gray-200">
                        <i class="fas fa-building text-gray-400 text-xl"></i>
                    </div>
                @endif
                
                <div class="flex-1">
                    @if(auth()->user()->unit)
                        <h3 class="font-bold text-lg text-gray-800">{{ auth()->user()->unit->nama_unit }}</h3>
                        <p class="text-sm text-gray-600">Direktur: {{ auth()->user()->unit->direktur }}</p>
                    @else
                        <h3 class="font-bold text-lg text-gray-800">Unit Tidak Tersedia</h3>
                        <p class="text-sm text-gray-600">Silahkan lengkapi profil unit</p>
                    @endif
                </div>
            </div>

            <!-- Unit Details -->
            @if(auth()->user()->unit)
                <div class="grid grid-cols-1 gap-2 mb-4 text-sm">
                    <div class="flex items-center border-b border-gray-100 py-2">
                        <div class="w-8 flex justify-center text-[#0a4a5a]">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <span class="text-gray-500 mr-2">Email:</span>
                            <span class="font-medium">{{ auth()->user()->email }}</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center border-b border-gray-100 py-2">
                        <div class="w-8 flex justify-center text-[#0a4a5a]">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <span class="text-gray-500 mr-2">Telepon:</span>
                            <span class="font-medium">{{ auth()->user()->unit->telepon }}</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center border-b border-gray-100 py-2">
                        <div class="w-8 flex justify-center text-[#0a4a5a]">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <span class="text-gray-500 mr-2">Dibuat:</span>
                            <span class="font-medium">{{ auth()->user()->unit->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                    
                    <div class="flex items-center py-2">
                        <div class="w-8 flex justify-center text-[#0a4a5a]">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <span class="text-gray-500 mr-2">Diperbarui:</span>
                            <span class="font-medium">{{ auth()->user()->unit->updated_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

    <!-- Right Side - Upload Form -->
    <div class="w-full md:w-1/2">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100">
            <div class="bg-gradient-to-r from-[#0a4a5a] to-[#116072] text-white p-3 rounded-t-lg flex justify-between items-center">
                <h2 class="text-lg font-semibold flex items-center">
                    <i class="fas fa-file-upload mr-2"></i> Upload Dokumen Baru
                </h2>
                <a href="{{ route('dokumen.index') }}" class="bg-white/10 hover:bg-white/20 text-white px-3 py-1 rounded text-sm font-medium transition flex items-center gap-1 backdrop-blur-sm">
                    <i class="fas fa-arrow-left text-xs"></i> Kembali
                </a>
            </div>

            <div class="p-4">
                @if ($errors->any())
                    <div class="bg-red-50 text-red-700 p-3 rounded mb-3 border-l-4 border-red-500 text-sm">
                        <div class="font-medium mb-1">Terjadi kesalahan:</div>
                        <ul class="list-disc pl-4 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('dokumen.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <!-- Hidden field for id_unit -->
                    <input type="hidden" name="id_unit" value="{{ auth()->user()->id_unit }}">
                    
                    <div class="space-y-1">
                        <label for="nama_dokumen" class="block text-sm font-medium text-gray-700">Nama Dokumen</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-file-alt text-gray-400"></i>
                            </div>
                            <input type="text" 
                                id="nama_dokumen" 
                                name="nama_dokumen" 
                                value="{{ old('nama_dokumen') }}" 
                                class="w-full pl-10 pr-3 py-2 border @error('nama_dokumen') border-red-300 @else border-gray-300 @enderror rounded focus:ring-[#0a4a5a] focus:border-[#0a4a5a] outline-none text-sm" 
                                placeholder="Masukkan nama dokumen"
                                required>
                        </div>
                        @error('nama_dokumen')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="file" class="block text-sm font-medium text-gray-700">File Dokumen</label>
                        <div class="relative bg-gray-50 border @error('file') border-red-300 @else border-gray-300 @enderror rounded">
                            <input type="file" 
                                id="file" 
                                name="file" 
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-l file:border-0 file:text-sm file:font-medium file:bg-[#0a4a5a]/10 file:text-[#0a4a5a] hover:file:bg-[#0a4a5a]/20 focus:outline-none"
                                required>
                        </div>
                        <p class="text-xs text-gray-500 flex items-center">
                            <i class="fas fa-info-circle mr-1"></i>
                            Format: PDF, DOC, DOCX, XLS, XLSX. Max: 10MB
                        </p>
                        @error('file')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full bg-[#0a4a5a] hover:bg-[#083b4a] text-white py-2 px-4 rounded transition flex items-center justify-center gap-2 text-sm font-medium shadow-sm">
                        <i class="fas fa-cloud-upload-alt"></i> Upload Dokumen
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Quick Info Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 mt-3 p-3">
            <div class="flex items-start">
                <div class="mr-3 mt-1 text-blue-500">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="text-sm text-gray-600">
                    <p class="mb-1">Dokumen yang diupload akan ditinjau oleh tim terkait sebelum disetujui.</p>
                    <p>Pastikan dokumen yang diupload sudah benar dan sesuai dengan ketentuan.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection