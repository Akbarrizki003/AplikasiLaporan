@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Edit Dokumen</h5>
                        <a href="{{ route('dokumen.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(in_array($dokumen->status, ['ditolak_manejer', 'ditolak_atasan']))
                        <div class="alert alert-warning">
                            <strong>Catatan Penolakan:</strong> {{ $dokumen->catatan }}
                        </div>
                    @endif

                    <form action="{{ route('dokumen.update', $dokumen->id_dokumen) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="nama_dokumen" class="form-label">Nama Dokumen</label>
                            <input type="text" class="form-control @error('nama_dokumen') is-invalid @enderror" 
                                id="nama_dokumen" name="nama_dokumen" value="{{ old('nama_dokumen', $dokumen->nama_dokumen) }}" required>
                            @error('nama_dokumen')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="file" class="form-label">File Dokumen</label>
                            <div class="mb-2">
                                <div class="d-flex align-items-center">
                                    @php
                                        $fileExtension = pathinfo(Storage::url($dokumen->file), PATHINFO_EXTENSION);
                                        $iconClass = 'bi-file-earmark';
                                        
                                        if (in_array($fileExtension, ['pdf'])) {
                                            $iconClass = 'bi-file-earmark-pdf';
                                        } elseif (in_array($fileExtension, ['doc', 'docx'])) {
                                            $iconClass = 'bi-file-earmark-word';
                                        } elseif (in_array($fileExtension, ['xls', 'xlsx'])) {
                                            $iconClass = 'bi-file-earmark-excel';
                                        }
                                    @endphp
                                    
                                    <i class="bi {{ $iconClass }} fs-2 me-2"></i>
                                    <span>{{ basename($dokumen->file) }}</span>
                                </div>
                            </div>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" 
                                id="file" name="file">
                            <small class="form-text text-muted">
                                Upload file baru untuk mengganti file lama. Format yang diperbolehkan: PDF, DOC, DOCX, XLS, XLSX. Ukuran maksimal: 10MB.
                                <br>
                                Biarkan kosong jika tidak ingin mengganti file.
                            </small>
                            @error('file')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection