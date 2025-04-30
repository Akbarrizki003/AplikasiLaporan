<div class="container mt-4">
    <h1 class="mb-4">Tambah Unit</h1>

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

    <form action="{{ route('units.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="nama_unit" class="form-label">Nama Unit</label>
            <input type="text" name="nama_unit" class="form-control" value="{{ old('nama_unit') }}" required>
        </div>

        <div class="mb-3">
            <label for="direktur" class="form-label">Direktur</label>
            <input type="text" name="direktur" class="form-control" value="{{ old('direktur') }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
        </div>

        <div class="mb-3">
            <label for="telepon" class="form-label">Telepon</label>
            <input type="text" name="telepon" class="form-control" value="{{ old('telepon') }}" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="logo" class="form-label">Logo (opsional)</label>
            <input type="file" name="logo" class="form-control">
        </div>

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="{{ route('units.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>