```blade
@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="bg-gradient-to-r from-[#0a4a5a] to-[#116072] text-white p-3 rounded-t-lg flex justify-between items-center">
            <h2 class="text-lg font-semibold flex items-center">
                <i class="fas fa-building mr-2"></i> Update Profil Unit
            </h2>
            <a href="{{ route('dokumen.create') }}" class="bg-white/10 hover:bg-white/20 text-white px-3 py-1 rounded text-sm font-medium transition flex items-center gap-1 backdrop-blur-sm">
                <i class="fas fa-arrow-left text-xs"></i> Kembali
            </a>
        </div>

        <div class="p-4">
            @if (session('success'))
                <div class="bg-green-50 text-green-700 p-3 rounded mb-4 border-l-4 border-green-500">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 text-red-700 p-3 rounded mb-4 border-l-4 border-red-500 text-sm">
                    <div class="font-medium mb-1">Terjadi kesalahan:</div>
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('unit.updateProfile') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <!-- Logo Unit -->
                <div class="mb-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Logo Unit</label>
                    <div class="flex items-center space-x-5">
                        <div class="flex-shrink-0">
                            @if($unit->logo)
                                <img src="{{ asset('storage/' . $unit->logo) }}" 
                                     alt="Logo Unit" 
                                     class="h-24 w-24 object-contain rounded-lg bg-gray-50 p-1 border border-gray-200">
                            @else
                                <div class="h-24 w-24 bg-gray-100 rounded-lg flex items-center justify-center border border-gray-200">
                                    <i class="fas fa-building text-gray-400 text-3xl"></i>
                                </div>
                            @endif
                        </div>
                        <div class="space-y-1 flex-1">
                            <div class="relative bg-gray-50 border @error('logo') border-red-300 @else border-gray-300 @enderror rounded">
                                <input type="file" 
                                    id="logo" 
                                    name="logo" 
                                    accept="image/jpeg,image/png,image/jpg"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-l file:border-0 file:text-sm file:font-medium file:bg-[#0a4a5a]/10 file:text-[#0a4a5a] hover:file:bg-[#0a4a5a]/20 focus:outline-none">
                            </div>
                            <p class="text-xs text-gray-500 flex items-center">
                                <i class="fas fa-info-circle mr-1"></i>
                                Format: JPG, JPEG, PNG. Max: 2MB
                            </p>
                        </div>
                    </div>
                    @error('logo')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Unit -->
                <div class="space-y-1">
                    <label for="nama_unit" class="block text-sm font-medium text-gray-700">Nama Unit <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-building text-gray-400"></i>
                        </div>
                        <input type="text" 
                            id="nama_unit" 
                            name="nama_unit" 
                            value="{{ old('nama_unit', $unit->nama_unit) }}" 
                            class="w-full pl-10 pr-3 py-2 border @error('nama_unit') border-red-300 @else border-gray-300 @enderror rounded focus:ring-[#0a4a5a] focus:border-[#0a4a5a] outline-none" 
                            placeholder="Masukkan nama unit"
                            required>
                    </div>
                    @error('nama_unit')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Direktur -->
                <div class="space-y-1">
                    <label for="direktur" class="block text-sm font-medium text-gray-700">Nama Direktur <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user-tie text-gray-400"></i>
                        </div>
                        <input type="text" 
                            id="direktur" 
                            name="direktur" 
                            value="{{ old('direktur', $unit->direktur) }}" 
                            class="w-full pl-10 pr-3 py-2 border @error('direktur') border-red-300 @else border-gray-300 @enderror rounded focus:ring-[#0a4a5a] focus:border-[#0a4a5a] outline-none" 
                            placeholder="Masukkan nama direktur"
                            required>
                    </div>
                    @error('direktur')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Telepon -->
                <div class="space-y-1">
                    <label for="telepon" class="block text-sm font-medium text-gray-700">Nomor Telepon <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-phone text-gray-400"></i>
                        </div>
                        <input type="text" 
                            id="telepon" 
                            name="telepon" 
                            value="{{ old('telepon', $unit->telepon) }}" 
                            class="w-full pl-10 pr-3 py-2 border @error('telepon') border-red-300 @else border-gray-300 @enderror rounded focus:ring-[#0a4a5a] focus:border-[#0a4a5a] outline-none" 
                            placeholder="Masukkan nomor telepon"
                            required>
                    </div>
                    @error('telepon')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex space-x-3 pt-3">
                    <button type="submit" class="flex-1 bg-[#0a4a5a] hover:bg-[#083b4a] text-white py-2 px-4 rounded transition flex items-center justify-center gap-2 text-sm font-medium shadow-sm">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('dokumen.create') }}" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded transition flex items-center justify-center gap-2 text-sm font-medium">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection