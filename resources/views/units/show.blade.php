@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Detail Unit</div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4 text-center">
                            @if ($unit->logo)
                                <img src="{{ asset('storage/' . $unit->logo) }}" alt="Logo Unit" class="img-fluid">
                            @else
                                <div class="alert alert-info">
                                    Tidak ada logo
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <h4>{{ $unit->nama_unit ?: 'Nama Unit Belum Diisi' }}</h4>
                            <p><strong>Direktur:</strong> {{ $unit->direktur ?: 'Belum Diisi' }}</p>
                            <p><strong>Telepon:</strong> {{ $unit->telepon ?: 'Belum Diisi' }}</p>
                            <p>
                                <strong>User Terhubung:</strong> 
                                @if ($unit->user)
                                    {{ $unit->user->name }} ({{ $unit->user->email }})
                                @else
                                    Tidak ada user terhubung
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <a href="{{ route('units.edit', $unit->id_unit) }}" class="btn btn-warning">Edit</a>
                            <a href="{{ route('units.index') }}" class="btn btn-secondary">Kembali</a>
                            <form action="{{ route('units.destroy', $unit->id_unit) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus unit ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection