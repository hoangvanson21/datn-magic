@extends('admin.main_admin')

@section('title', 'Chỉnh sửa thông số')

@section('content')
<div class="container-fluid mt-4">
    <div class="bg-white p-5 rounded shadow-md">
    <h2 class="text-xxl font-bold mb-4 text-center">CHỈNH SỬA THÔNG SỐ</h2>

        <form action="{{ route('admin.options.update', $options->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- RAM -->
            <div class="mb-4">
                <label for="ram" class="form-label">RAM</label>
                <select name="ram" id="ram" class="form-control @error('ram') is-invalid @enderror" required>
                    @foreach ($ramOptions as $ram)
                        <option value="{{ $ram }}" {{ old('ram', $options->ram) == $ram ? 'selected' : '' }}>{{ $ram }}</option>
                    @endforeach
                </select>
                @error('ram')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- ROM -->
            <div class="mb-4">
                <label for="rom" class="form-label">ROM</label>
                <select name="rom" id="rom" class="form-control @error('rom') is-invalid @enderror" required>
                    @foreach ($romOptions as $rom)
                        <option value="{{ $rom }}" {{ old('rom', $options->rom) == $rom ? 'selected' : '' }}>{{ $rom }}</option>
                    @endforeach
                </select>
                @error('rom')
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
