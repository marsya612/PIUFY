@extends('layouts.app')

@section('title', 'Profile Pengguna')

@section('content')
<div class="p-4">

    <h3 class="fw-bold mb-4">Profile Pengguna</h3>

    <div class="card shadow-sm border-0 rounded-4 p-4">
        <div class="row align-items-center">

            <!-- FOTO -->
            <!-- <div class="col-md-4 text-center">

                @if($user->photo)
                    <!-- <img src="{{ asset('storage/' . $user->photo) }}" -->
                    <img src="{{ $user->photo }}" alt="foto profil" class="rounded-circle" style="width:200px; height:200px; object-fit:cover;">
                        class="rounded-circle"
                        style="width:200px; height:200px; object-fit:cover;">
                @else
                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                        style="width:200px; height:200px; margin:auto;">
                        <i class="bi bi-person" style="font-size:80px; color:#bbb;"></i>
                    </div>
                @endif

            </div> -->
            <div class="col-md-4 text-center">
                @if($user->photo)
                    <img src="{{ $user->photo }}" 
                         alt="foto profil" 
                         class="rounded-circle" 
                         style="width:200px; height:200px; object-fit:cover;">
                @else
                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                        style="width:200px; height:200px; margin:auto;">
                        <i class="bi bi-person" style="font-size:80px; color:#bbb;"></i>
                    </div>
                @endif
            </div>

            <!-- DATA -->
            <div class="col-md-8">

                <div class="mb-3">
                    <label>Nama</label>
                    <input type="text" class="form-control" value="{{ $user->name }}" readonly>
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    <input type="text" class="form-control" value="{{ $user->email }}" readonly>
                </div>

                <div class="mb-3">
                    <label>No Telepon</label>
                    <input type="text" class="form-control" value="{{ $user->phone }}" readonly>
                </div>

                <div class="mb-3">
                    <label>Jabatan</label>
                    <input type="text" class="form-control" value="{{ $user->jabatan }}" readonly>
                </div>

                <div class="mb-3">
                    <label>Divisi</label>
                    <input type="text" class="form-control" value="{{ $user->divisi }}" readonly>
                </div>

                <div class="mb-3">
                    <label>Bergabung</label>
                    <input type="text" class="form-control" 
                           value="{{ $user->created_at->format('d M Y') }}" readonly>
                </div>

                <!-- EDIT BUTTON -->
                <a href="{{ route('profile.edit') }}" 
                   class="btn w-100 text-white mt-3"
                   style="background:#5c5757;">
                    Edit Profile
                </a>

                <!-- LOGOUT BUTTON -->
                <form action="{{ route('logout') }}" method="POST" class="mt-2">
                    @csrf
                    <button type="submit" class="btn btn-danger w-100">
                        Logout
                    </button>
                </form>

            </div>

        </div>
    </div>

</div>
@endsection
