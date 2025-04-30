<div class="container mt-4">
    <h1 class="mb-4">Daftar Unit</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('units.create') }}" class="btn btn-primary mb-3">Tambah Unit</a>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Nama Unit</th>
                <th>Direktur</th>
                <th>Email</th>
                <th>Telepon</th>
                <th>Logo</th>
                <th>Aksi</th>

            </tr>
        </thead>
        <tbody>
            @forelse ($units as $unit)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $unit->nama_unit }}</td>
                    <td>{{ $unit->direktur }}</td>
                    <td>{{ $unit->email }}</td>
                    <td>{{ $unit->telepon }}</td>
                    
                    <td>
                        @if($unit->logo)
                            <img src="{{ asset($unit->logo) }}" alt="Logo" width="60">
                        @else
                            Tidak ada logo
                        @endif
                    </td>
                    <td>
                        <!-- Tombol Edit -->
                        <a href="{{ route('units.edit', $unit->id_unit) }}" class="btn btn-warning btn-sm">Edit</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Belum ada data unit.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>