@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Tambah Unit Baru</div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('units.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group row mb-3">
                            <label for="id_user" class="col-md-4 col-form-label text-md-right">User Unit</label>
                            <div class="col-md-6">
                                <select id="id_user" class="form-control @error('id_user') is-invalid @enderror" name="id_user" required>
                                    <option value="">Pilih User</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ old('id_user') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_user')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="nama_unit" class="col-md-4 col-form-label text-md-right">Nama Unit</label>
                            <div class="col-md-6">
                                <input id="nama_unit" type="text" class="form-control @error('nama_unit') is-invalid @enderror" name="nama_unit" value="{{ old('nama_unit') }}" required autofocus>
                                @error('nama_unit')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="direktur" class="col-md-4 col-form-label text-md-right">Direktur</label>
                            <div class="col-md-6">
                                <input id="direktur" type="text" class="form-control @error('direktur') is-invalid @enderror" name="direktur" value="{{ old('direktur') }}" required>
                                @error('direktur')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="telepon" class="col-md-4 col-form-label text-md-right">Telepon</label>
                            <div class="col-md-6">
                                <input id="telepon" type="text" class="form-control @error('telepon') is-invalid @enderror" name="telepon" value="{{ old('telepon') }}" required>
                                @error('telepon')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="logo" class="col-md-4 col-form-label text-md-right">Logo</label>
                            <div class="col-md-6">
                                <input id="logo" type="file" class="form-control @error('logo') is-invalid @enderror" name="logo">
                                <small class="form-text text-muted">Format: JPG, PNG, JPEG (Maks: 2MB)</small>
                                @error('logo')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Simpan
                                </button>
                                <a href="{{ route('units.index') }}" class="btn btn-secondary">
                                    Kembali
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection