@extends('layouts.app')
@section('title', 'Edit Profile Pengguna')
@section('content')
<div class="p-4">
    <h4 class="fw-semibold mb-4">Edit Profile Pengguna</h4>

    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PUT')

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">

                {{-- Header --}}
                <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center"
                        style="width:52px; height:52px; min-width:52px;">
                        <span class="text-white fw-bold fs-4">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <div class="fw-semibold fs-5">{{ $user->name }}</div>
                        <div class="text-muted small">{{ $user->jabatan }} &mdash; {{ $user->divisi }}</div>
                    </div>
                </div>

                {{-- Form Fields --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Nama</label>
                        <input type="text" name="name" class="form-control" value="{{ $user->name }}">
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $user->email }}">
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">No Telepon</label>
                        <input type="text" name="phone" class="form-control" value="{{ $user->phone ?? '' }}">
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">Jabatan</label>
                        <input type="text" name="jabatan" class="form-control" value="{{ $user->jabatan ?? '' }}">
                    </div>
                    <div class="col-md-12">
                        <label class="text-muted small mb-1">Divisi</label>
                        <input type="text" name="divisi" class="form-control" value="{{ $user->divisi ?? '' }}">
                    </div>
                </div>

                {{-- Actions --}}
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-dark px-4">
                        <i class="bi bi-check-lg me-1"></i> Simpan
                    </button>
                    <a href="{{ route('profile') }}" class="btn btn-outline-secondary px-4">
                        <i class="bi bi-x me-1"></i> Batal
                    </a>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection
