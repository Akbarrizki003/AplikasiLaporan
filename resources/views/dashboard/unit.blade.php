@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-between mb-4">
        <div class="col-md-6">
            <h2>Dashboard Unit</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('dokumen.create') }}" class="btn btn-primary">Upload Laporan Baru</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Daftar Laporan</div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Dokumen</th>
                            <th>Tanggal Upload</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dokumen as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->nama_dokumen }}</td>
                            <td>{{ $item->tanggal_upload }}</td>
                            <td>
                                @if($item->status == 'dikirim')
                                    <span class="badge bg-primary">Dikirim</span>
                                @elseif($item->status == 'diterima_keuangan')
                                    <span class="badge bg-info">Diterima Keuangan</span>
                                @elseif($item->status == 'diteruskan_ke_manejer')
                                    <span class="badge bg-warning">Diteruskan ke Manajer</span>
                                @elseif($item->status == 'disetujui_manejer')
                                    <span class="badge bg-success">Disetujui Manajer</span>
                                @elseif($item->status == 'ditolak_manejer')
                                    <span class="badge bg-danger">Ditolak Manajer</span>
                                @elseif($item->status == 'diteruskan_ke_atasan')
                                    <span class="badge bg-warning">Diteruskan ke Atasan</span>
                                @elseif($item->status == 'disetujui_atasan')
                                    <span class="badge bg-success">Disetujui Atasan</span>
                                @elseif($item->status == 'ditolak_atasan')
                                    <span class="badge bg-danger">Ditolak Atasan</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('dokumen.show', $item->id_dokumen) }}" class="btn btn-sm btn-primary">Detail</a>
                                <a href="{{ route('dokumen.download', $item->id_dokumen) }}" class="btn btn-sm btn-success">Download</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Belum ada dokumen</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection