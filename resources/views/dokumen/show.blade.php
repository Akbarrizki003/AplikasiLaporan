@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Detail Dokumen</h5>
                        <a href="{{ route('dokumen.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Nama Dokumen:</div>
                        <div class="col-md-8">{{ $dokumen->nama_dokumen }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Unit:</div>
                        <div class="col-md-8">{{ $dokumen->unit->nama_unit }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Tanggal Upload:</div>
                        <div class="col-md-8">{{ \Carbon\Carbon::parse($dokumen->tanggal_upload)->format('d M Y') }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Status:</div>
                        <div class="col-md-8">
                            @switch($dokumen->status)
                                @case('dikirim')
                                    <span class="badge bg-primary">Dikirim</span>
                                    @break
                                @case('diterima_keuangan')
                                    <span class="badge bg-info">Diterima Keuangan</span>
                                    @break
                                @case('diteruskan_ke_manejer')
                                    <span class="badge bg-secondary">Diteruskan ke Manajer</span>
                                    @break
                                @case('disetujui_manejer')
                                    <span class="badge bg-success">Disetujui Manajer</span>
                                    @break
                                @case('ditolak_manejer')
                                    <span class="badge bg-danger">Ditolak Manajer</span>
                                    @break
                                @case('diteruskan_ke_atasan')
                                    <span class="badge bg-secondary">Diteruskan ke Atasan</span>
                                    @break
                                @case('disetujui_atasan')
                                    <span class="badge bg-success">Disetujui Atasan</span>
                                    @break
                                @case('ditolak_atasan')
                                    <span class="badge bg-danger">Ditolak Atasan</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">{{ $dokumen->status }}</span>
                            @endswitch
                        </div>
                    </div>

                    @if($dokumen->catatan && in_array($dokumen->status, ['ditolak_manejer', 'ditolak_atasan']))
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Catatan Penolakan:</div>
                            <div class="col-md-8">
                                <div class="alert alert-warning">
                                    {{ $dokumen->catatan }}
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">File:</div>
                        <div class="col-md-8">
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
                                
                                @if(Auth::user()->role === 'unit' && $dokumen->id_unit === Auth::user()->id_unit)
                                    <a href="{{ route('dokumen.download', $dokumen->id_dokumen) }}" class="btn btn-sm btn-success">
                                        <i class="bi bi-download"></i> Download
                                    </a>
                                @else
                                    <a href="{{ route('dokumen.viewDokumen', $dokumen->id_dokumen) }}" class="btn btn-sm btn-info" target="_blank">
                                        <i class="bi bi-eye"></i> Lihat Dokumen
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="d-flex mt-4">
                        @if(Auth::user()->role === 'unit' && Auth::user()->id_unit === $dokumen->id_unit)
                            @if(in_array($dokumen->status, ['dikirim', 'ditolak_manejer', 'ditolak_atasan']))
                                <a href="{{ route('dokumen.edit', $dokumen->id_dokumen) }}" class="btn btn-warning me-2">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                
                                <form action="{{ route('dokumen.destroy', $dokumen->id_dokumen) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?')">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </form>
                            @endif
                        @endif

                        @if(Auth::user()->role === 'keuangan')
                            @if($dokumen->status === 'dikirim')
                                <form action="{{ route('dokumen.terimaKeuangan', $dokumen->id_dokumen) }}" method="POST" class="me-2">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-circle"></i> Terima
                                    </button>
                                </form>
                            @endif

                            @if($dokumen->status === 'diterima_keuangan')
                                <form action="{{ route('dokumen.teruskanKeManajer', $dokumen->id_dokumen) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-forward"></i> Teruskan ke Manajer
                                    </button>
                                </form>
                            @endif
                        @endif

                        @if(Auth::user()->role === 'manajer')
                            @if($dokumen->status === 'diteruskan_ke_manejer')
                                <form action="{{ route('dokumen.setujuiManajer', $dokumen->id_dokumen) }}" method="POST" class="me-2">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-circle"></i> Setujui
                                    </button>
                                </form>

                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#tolakModal">
                                    <i class="bi bi-x-circle"></i> Tolak
                                </button>

                                <!-- Modal Tolak -->
                                <div class="modal fade" id="tolakModal" tabindex="-1" aria-labelledby="tolakModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="tolakModalLabel">Tolak Dokumen</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('dokumen.tolakManajer', $dokumen->id_dokumen) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="catatan" class="form-label">Catatan Penolakan</label>
                                                        <textarea class="form-control" id="catatan" name="catatan" rows="3" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-danger">Tolak Dokumen</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($dokumen->status === 'disetujui_manejer')
                                <form action="{{ route('dokumen.teruskanKeAtasan', $dokumen->id_dokumen) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-forward"></i> Teruskan ke Atasan
                                    </button>
                                </form>
                            @endif
                        @endif

                        @if(Auth::user()->role === 'atasan')
                            @if($dokumen->status === 'diteruskan_ke_atasan')
                                <form action="{{ route('dokumen.setujuiAtasan', $dokumen->id_dokumen) }}" method="POST" class="me-2">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-circle"></i> Setujui
                                    </button>
                                </form>

                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#tolakModal">
                                    <i class="bi bi-x-circle"></i> Tolak
                                </button>

                                <!-- Modal Tolak -->
                                <div class="modal fade" id="tolakModal" tabindex="-1" aria-labelledby="tolakModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="tolakModalLabel">Tolak Dokumen</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('dokumen.tolakAtasan', $dokumen->id_dokumen) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="catatan" class="form-label">Catatan Penolakan</label>
                                                        <textarea class="form-control" id="catatan" name="catatan" rows="3" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-danger">Tolak Dokumen</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection