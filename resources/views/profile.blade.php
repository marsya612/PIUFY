@extends('layouts.app')
@section('title', 'Profile Pengguna')
@section('content')
<div class="p-4">
    <h4 class="fw-semibold mb-4">Profile Pengguna</h4>

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

            {{-- Info --}}
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="text-muted small mb-1">Nama</label>
                    <div class="fw-medium">{{ $user->name }}</div>
                </div>
                <div class="col-md-6">
                    <label class="text-muted small mb-1">Email</label>
                    <div class="fw-medium">{{ $user->email }}</div>
                </div>
                <div class="col-md-6">
                    <label class="text-muted small mb-1">No Telepon</label>
                    <div class="fw-medium">{{ $user->phone ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                    <label class="text-muted small mb-1">Jabatan</label>
                    <div class="fw-medium">{{ $user->jabatan ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                    <label class="text-muted small mb-1">Divisi</label>
                    <div class="fw-medium">{{ $user->divisi ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                    <label class="text-muted small mb-1">Bergabung</label>
                    <div class="fw-medium">{{ $user->created_at->format('d M Y') }}</div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="d-flex gap-2">
                <a href="{{ route('profile.edit') }}"
                   class="btn btn-dark px-4">
                    <i class="bi bi-pencil me-1"></i> Edit Profile
                </a>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger px-4">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
