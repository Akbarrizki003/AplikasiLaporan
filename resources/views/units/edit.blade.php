<div class="container mt-4">
    <h1 class="mb-4">Edit Unit</h1>

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

    <form action="{{ route('units.update', $unit->id_unit) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nama_unit" class="form-label">Nama Unit</label>
            <input type="text" name="nama_unit" class="form-control" value="{{ old('nama_unit', $unit->nama_unit) }}" required>
        </div>

        <div class="mb-3">
            <label for="direktur" class="form-label">Direktur</label>
            <input type="text" name="direktur" class="form-control" value="{{ old('direktur', $unit->direktur) }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $unit->email) }}" required>
        </div>

        <div class="mb-3">
            <label for="telepon" class="form-label">Telepon</label>
            <input type="text" name="telepon" class="form-control" value="{{ old('telepon', $unit->telepon) }}" required>
        </div>

       
        <div class="mb-3">
            <label for="logo" class="form-label">Logo (opsional)</label>
            <input type="file" name="logo" class="form-control">
        </div>

        @if($unit->logo)
            <div class="mb-3">
                <label class="form-label">Logo Saat Ini:</label>
                <img src="{{ asset($unit->logo) }}" alt="Logo" width="60">
            </div>
        @endif

        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
        <a href="{{ route('units.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>