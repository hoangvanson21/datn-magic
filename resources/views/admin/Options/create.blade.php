@extends('admin.main_admin')

@section('title', 'Thêm thông số')

@section('content')
<div class="container-fluid mt-4">
    <h2 class="text-2xl font-bold mb-4 text-center">Thêm thông số mới</h2>

    <div class="bg-white p-5 rounded shadow-md">
        <form action="{{ route('admin.options.store') }}" method="POST">
            @csrf
            <!-- Tên thông số -->
            <div class="mb-4">
                <label for="ram" class="form-label">RAM</label>
                <select name="ram" id="ram" class="form-control @error('ram') is-invalid @enderror" required>
                    <option value="">Chọn RAM</option>
                    <option value="4" {{ old('ram') == '4' ? 'selected' : '' }}>4 </option>
                    <option value="6" {{ old('ram') == '6' ? 'selected' : '' }}>6 </option>
                    <option value="8" {{ old('ram') == '8' ? 'selected' : '' }}>8 </option>
                    <option value="12" {{ old('ram') == '12' ? 'selected' : '' }}>12 </option>
                    <option value="16" {{ old('ram') == '16' ? 'selected' : '' }}>16 </option>
                </select>
                @error('ram')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="rom" class="form-label">ROM</label>
                <select name="rom" id="rom" class="form-control @error('rom') is-invalid @enderror" required>
                    <option value="">Chọn ROM</option>
                    <option value="32" {{ old('rom') == '32' ? 'selected' : '' }}>32 </option>
                    <option value="64" {{ old('rom') == '64' ? 'selected' : '' }}>64 </option>
                    <option value="128" {{ old('rom') == '128' ? 'selected' : '' }}>128 </option>
                    <option value="256" {{ old('rom') == '256' ? 'selected' : '' }}>256 </option>
                    <option value="512" {{ old('rom') == '512' ? 'selected' : '' }}>512 </option>
                </select>
                @error('rom')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
           

            <!-- Nút Thêm và Trở Về -->
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.options.index') }}" class="btn btn-secondary">Trở về</a>
                <button type="submit" class="btn btn-success">Thêm thông số</button>
            </div>
        </form>
    </div>
</div>
@endsection
