<div class="container mt-4">
    <h1 class="mb-4">Daftar Dokumen</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('dokumens.create') }}" class="btn btn-primary mb-3">Tambah Dokumen</a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Dokumen</th>
                <th>Unit</th>
                <th>Tanggal Upload</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dokumens as $dokumen)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $dokumen->nama_dokumen }}</td>
                    <td>{{ $dokumen->unit->nama_unit }}</td>
                    <td>{{ $dokumen->tanggal_upload }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $dokumen->status)) }}</td>
                    <td>
                        <a href="{{ asset('storage/' . $dokumen->file) }}" class="btn btn-info btn-sm" target="_blank">Lihat</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>