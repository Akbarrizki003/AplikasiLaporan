@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Daftar Unit</span>
                    <a href="{{ route('units.create') }}" class="btn btn-primary btn-sm">Tambah Unit</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Unit</th>
                                    <th>Direktur</th>
                                    <th>User</th>
                                    <th>Telepon</th>
                                    <th>Logo</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($units as $key => $unit)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $unit->nama_unit ?: 'Belum diisi' }}</td>
                                        <td>{{ $unit->direktur ?: 'Belum diisi' }}</td>
                                        <td>{{ $unit->user ? $unit->user->name : 'Tidak terhubung' }}</td>
                                        <td>{{ $unit->telepon ?: 'Belum diisi' }}</td>
                                        <td>
                                            @if ($unit->logo)
                                                <img src="{{ asset('storage/' . $unit->logo) }}" alt="Logo Unit" width="50">
                                            @else
                                                Tidak ada logo
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('units.show', $unit->id_unit) }}" class="btn btn-info btn-sm">Detail</a>
                                                <a href="{{ route('units.edit', $unit->id_unit) }}" class="btn btn-warning btn-sm">Edit</a>
                                                <form action="{{ route('units.destroy', $unit->id_unit) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus unit ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada data unit</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection