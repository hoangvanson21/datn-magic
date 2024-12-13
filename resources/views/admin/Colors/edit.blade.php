@extends('admin.main_admin')

@section('title', 'Chỉnh sửa màu sắc')

@section('content')
<div class="container-fluid mt-4">
    <div class="bg-white p-5 rounded shadow-md">
        <h1 class="text-2xl font-bold mb-4 text-center">Chỉnh sửa màu sắc</h1>
        <form action="{{ route('admin.colors.update', $colors->id) }}" method="POST">
            @csrf
            @method('PUT')
            <!-- Tên Danh Mục -->
            <div class="mb-4">
                <label for="name" class="form-label">Tên Danh Mục</label>
                <input type="text" id="name" name="name" value="{{ old('name', $colors->name) }}" class="form-control @error('name') is-invalid @enderror">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Nút Cập Nhật -->
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Cập Nhật</button>
            </div>
        </form>
    </div>
</div>
@endsection
