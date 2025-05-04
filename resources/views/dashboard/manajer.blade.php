@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Dashboard Manajer</h2>
    
    <div class="card">
        <div class="card-header">Dokumen yang Perlu Diproses</div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Unit</th>
                            <th>Nama Dokumen</th>
                            <th>Tanggal Upload</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dokumen as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->unit->nama_unit }}</td>
                            <td>{{ $item->nama_dokumen }}</td>
                            <td>{{ $item->tanggal_upload }}</td>
                            <td>
                                <a href="{{ route('dokumen.show', $item->id_dokumen) }}" class="btn btn-sm btn-primary">Detail</a>
                                <a href="{{ route('dokumen.download', $item->id_dokumen) }}" class="btn btn-sm btn-success">Download</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada dokumen yang perlu diproses</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection