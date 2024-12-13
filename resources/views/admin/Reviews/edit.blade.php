@extends('admin.main_admin')

@section('title', 'Sửa Bình Luận')

@section('content')
<div class="container-fluid mt-4">
    <h2 class="text-xxl font-bold mb-4 text-center">SỬA BÌNH LUẬN</h2>

    <!-- Form sửa bình luận -->
    <div class="row">
        <div class="col-md-12">
            <form action="{{ route('admin.reviews.update', $review->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Người dùng -->
                <div class="mb-3">
                    <label for="user_name" class="form-label fw-bold">Người Dùng</label>
                    <input type="text" id="user_name" class="form-control" value="{{ $review->user->name }}" readonly>
                </div>

                <!-- Bình luận -->
                <div class="mb-3">
                    <label for="review_text" class="form-label fw-bold">Bình Luận</label>
                    <textarea id="review_text" name="review_text" rows="4" class="form-control @error('review_text') is-invalid @enderror">{{ old('review_text', $review->review_text) }}</textarea>
                    @error('review_text')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Đánh giá -->
                <div class="mb-3">
                    <label for="rating" class="form-label fw-bold">Đánh Giá (1-5)</label>
                    <input type="number" id="rating" name="rating" min="1" max="5" class="form-control @error('rating') is-invalid @enderror" value="{{ old('rating', $review->rating) }}">
                    @error('rating')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Trạng thái -->
                <div class="mb-3">
                    <label for="status" class="form-label fw-bold">Trạng Thái</label>
                    <select id="status" name="status" class="form-control @error('status') is-invalid @enderror">
                        <option value="0" {{ old('status', $review->status) == 0 ? 'selected' : '' }}>Hiện</option>
                        <option value="1" {{ old('status', $review->status) == 1 ? 'selected' : '' }}>Ẩn</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Nút hành động -->
                <div class="d-flex justify-content-center">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-save"></i> Lưu Thay Đổi
                    </button>
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay Lại
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
