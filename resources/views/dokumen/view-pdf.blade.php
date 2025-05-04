@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Lihat Dokumen: {{ $dokumen->nama_dokumen }}</h5>
                        <div>
                            <a href="{{ url()->previous() }}" class="btn btn-secondary me-2">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            
                            @if(Auth::user()->role === 'keuangan')
                                @if($dokumen->status === 'dikirim')
                                    <form action="{{ route('dokumen.terimaKeuangan', $dokumen->id_dokumen) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success">
                                            <i class="bi bi-check-circle"></i> Terima
                                        </button>
                                    </form>
                                @endif

                                @if($dokumen->status === 'diterima_keuangan')
                                    <form action="{{ route('dokumen.teruskanKeManajer', $dokumen->id_dokumen) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-forward"></i> Teruskan ke Manajer
                                        </button>
                                    </form>
                                @endif
                            @endif

                            @if(Auth::user()->role === 'manajer')
                                @if($dokumen->status === 'diteruskan_ke_manejer')
                                    <form action="{{ route('dokumen.setujuiManajer', $dokumen->id_dokumen) }}" method="POST" class="d-inline me-2">
                                        @csrf
                                        <button type="submit" class="btn btn-success">
                                            <i class="bi bi-check-circle"></i> Setujui
                                        </button>
                                    </form>

                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#tolakModal">
                                        <i class="bi bi-x-circle"></i> Tolak
                                    </button>
                                @endif

                                @if($dokumen->status === 'disetujui_manejer')
                                    <form action="{{ route('dokumen.teruskanKeAtasan', $dokumen->id_dokumen) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-forward"></i> Teruskan ke Atasan
                                        </button>
                                    </form>
                                @endif
                            @endif

                            @if(Auth::user()->role === 'atasan')
                                @if($dokumen->status === 'diteruskan_ke_atasan')
                                    <form action="{{ route('dokumen.setujuiAtasan', $dokumen->id_dokumen) }}" method="POST" class="d-inline me-2">
                                        @csrf
                                        <button type="submit" class="btn btn-success">
                                            <i class="bi bi-check-circle"></i> Setujui
                                        </button>
                                    </form>

                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#tolakModal">
                                        <i class="bi bi-x-circle"></i> Tolak
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if(session('success'))
                        <div class="alert alert-success m-3">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger m-3">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="embed-responsive">
                        <iframe src="{{ $fileUrl }}" style="width:100%; height:80vh;" frameborder="0"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tolak untuk Manajer -->
@if(Auth::user()->role === 'manajer' && $dokumen->status === 'diteruskan_ke_manejer')
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

<!-- Modal Tolak untuk Atasan -->
@if(Auth::user()->role === 'atasan' && $dokumen->status === 'diteruskan_ke_atasan')
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
@endsection