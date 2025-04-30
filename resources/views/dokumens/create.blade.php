<div class="container mt-4">
    <h1 class="mb-4">Tambah Dokumen</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('dokumens.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="id_unit" class="form-label">Unit</label>
            <select name="id_unit" class="form-control" required>
                <option value="">Pilih Unit</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id_unit }}">{{ $unit->nama_unit }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="nama_dokumen" class="form-label">Nama Dokumen</label>
            <input type="text" name="nama_dokumen" class="form-control" value="{{ old('nama_dokumen') }}" required>
        </div>

        <div class="mb-3">
            <label for="tanggal_upload" class="form-label">Tanggal Upload</label>
            <input type="date" name="tanggal_upload" class="form-control" value="{{ old('tanggal_upload') }}" required>
        </div>

        <div class="mb-3">
            <label for="file" class="form-label">File Dokumen</label>
            <input type="file" name="file" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" class="form-control" required>
                <option value="dikirim">Dikirim</option>
                <option value="diterima_keuangan">Diterima Keuangan</option>
                <option value="diteruskan_ke_manejer">Diteruskan ke Manejer</option>
                <option value="disetujui_manejer">Disetujui Manejer</option>
                <option value="ditolak_manejer">Ditolak Manejer</option>
                <option value="diteruskan_ke_atasan">Diteruskan ke Atasan</option>
                <option value="disetujui_atasan">Disetujui Atasan</option>
                <option value="ditolak_atasan">Ditolak Atasan</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="catatan" class="form-label">Catatan</label>
            <textarea name="catatan" class="form-control">{{ old('catatan') }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Simpan Dokumen</button>
        <a href="{{ route('dokumens.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>