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

                    <div class="text-center mb-3">
                        <h6>Informasi Dokumen</h6>
                        <p class="mb-1"><strong>Nama Dokumen:</strong> {{ $dokumen->nama_dokumen }}</p>
                        <p class="mb-1"><strong>Unit:</strong> {{ $dokumen->unit->nama_unit }}</p>
                        <p class="mb-1"><strong>Tanggal Upload:</strong> {{ \Carbon\Carbon::parse($dokumen->tanggal_upload)->format('d M Y') }}</p>
                        <p class="mb-1">
                            <strong>Status:</strong> 
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
                        </p>
                    </div>

                    <div class="alert alert-info">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-info-circle-fill me-2 fs-4"></i>
                            <div>
                                @if(in_array($fileExtension, ['doc', 'docx']))
                                    <p class="mb-0">Dokumen Microsoft Word tidak dapat ditampilkan langsung di browser. Silakan gunakan Office Online atau aplikasi Office untuk membuka dokumen ini.</p>
                                @elseif(in_array($fileExtension, ['xls', 'xlsx']))
                                    <p class="mb-0">Dokumen Microsoft Excel tidak dapat ditampilkan langsung di browser. Silakan gunakan Office Online atau aplikasi Office untuk membuka dokumen ini.</p>
                                @else
                                    <p class="mb-0">Format dokumen ini tidak dapat ditampilkan langsung di browser.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ $fileUrl }}" class="btn btn-primary" download>
                            <i class="bi bi-download"></i> Download Dokumen
                        </a>
                    </div>

                    @if(in_array($fileExtension, ['doc', 'docx', 'xls', 'xlsx']))
                        <div class="card mt-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Coba Lihat dengan Office Online</h6>
                            </div>
                            <div class="card-body">
                                <div class="embed-responsive">
                                    @php
                                        $officeOnlineUrl = '';
                                        if(in_array($fileExtension, ['doc', 'docx'])) {
                                            $officeOnlineUrl = 'https://view.officeapps.live.com/op/embed.aspx?src=' . urlencode(url($fileUrl));
                                        } elseif(in_array($fileExtension, ['xls', 'xlsx'])) {
                                            $officeOnlineUrl = 'https://view.officeapps.live.com/op/embed.aspx?src=' . urlencode(url($fileUrl));
                                        }
                                    @endphp
                                    <iframe src="{{ $officeOnlineUrl }}" style="width:100%; height:500px;" frameborder="0"></iframe>
                                </div>
                                <p class="text-muted text-center mt-2">
                                    <small>Preview disediakan oleh Microsoft Office Online. Tampilan mungkin berbeda dengan file aslinya.</small>
                                </p>
                            </div>
                        </div>
                    @endif
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