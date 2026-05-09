@extends('layouts.app')

@section('title', 'Edit Profile Pengguna')

@section('content')
<div class="p-4">

    <h3 class="fw-bold mb-4">Edit Profile Pengguna</h3>
    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card shadow-sm border-0 rounded-4 p-4">
            <div class="row align-items-center">

                <!-- FOTO -->
                <!-- <div class="col-md-4 text-center">

                    @if($user->photo)
                        <img id="previewImage"
                            src="{{ asset('storage/' . $user->photo) }}"
                            src="{{ $user->photo }}" 
                            class="rounded-circle shadow"
                            style="width:200px; height:200px; object-fit:cover;">
                    @else
                        <div id="previewContainer"
                            class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                            style="width:200px; height:200px; margin:auto;">
                            <i class="bi bi-person" style="font-size:80px; color:#bbb;"></i>
                        </div>

                        <img id="previewImage"
                            class="rounded-circle shadow"
                            style="display:none; width:200px; height:200px; object-fit:cover;">
                    @endif

                    <div class="mt-3">
                        <input type="file" name="photo" class="form-control" accept="image/*" onchange="previewFoto(event)">
                    </div>

                </div> -->
                <!-- <div class="col-md-4 text-center">
                    @if($user->photo)
                        <img id="previewImage"
                            src="{{ $user->photo }}" 
                            class="rounded-circle shadow"
                            style="width:200px; height:200px; object-fit:cover;">
                    @else
                        <div id="previewContainer"
                            class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                            style="width:200px; height:200px; margin:auto;">
                            <i class="bi bi-person" style="font-size:80px; color:#bbb;"></i>
                        </div>
                        <img id="previewImage"
                            class="rounded-circle shadow"
                            style="display:none; width:200px; height:200px; object-fit:cover;">
                    @endif
                    <div class="mt-3">
                        <input type="file" name="photo" class="form-control" accept="image/*" onchange="previewFoto(event)">
                    </div>
                </div> -->

                <!-- DATA -->
                <div class="col-md-8">

                    <div class="mb-3">
                        <label>Nama</label>
                        <input type="text" name="name" class="form-control" value="{{ $user->name }}">
                    </div>

                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $user->email }}">
                    </div>

                    <div class="mb-3">
                        <label>No Telepon</label>
                        <input type="text" name="phone" class="form-control" value="{{ $user->phone }}">
                    </div>

                    <div class="mb-3">
                        <label>Jabatan</label>
                        <input type="text" name="jabatan" class="form-control" value="{{ $user->jabatan }}">
                    </div>

                    <div class="mb-3">
                        <label>Divisi</label>
                        <input type="text" name="divisi" class="form-control" value="{{ $user->divisi }}">
                    </div>

                    <!-- BUTTON -->
                    <button type="submit" class="btn w-100 text-white mt-3" style="background:#5c5757;">
                        Save
                    </button>

                </div>

            </div>
        </div>

    </form>


    <script>
function previewFoto(event) {
    const file = event.target.files[0];
    const reader = new FileReader();

    reader.onload = function(e) {
        const img = document.getElementById('previewImage');
        const container = document.getElementById('previewContainer');

        if (container) container.style.display = 'none';

        img.src = e.target.result;
        img.style.display = 'block';
    }

    if (file) {
        reader.readAsDataURL(file);
    }
}
</script>
</div>
@endsection

