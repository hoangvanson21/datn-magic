@extends('admin.main_admin')

@section('title', 'Danh sách Danh Mục')

@section('content')
<div class="container-fluid mt-4">
    <h2 class="text-xxl font-bold mb-4 text-center">DANH SÁCH DANH MỤC</h2>

    <!-- Nút thêm và thùng rác -->
    <div class="row mb-3">
        <div class="col-md-6">
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Thêm Danh Mục
            </a>
           
        </div>

        <!-- Form tìm kiếm -->
        <div class="col-md-6">
            <form action="{{ route('admin.categories.index') }}" method="GET" class="d-flex justify-content-end" style="display: flex">
                <input type="text" name="search" placeholder="Tìm kiếm danh mục" class="form-control me-2" value="{{ request()->get('search') }}">
                <button type="submit" class="btn btn-info">
                    <i class="fas fa-search"></i> Tìm kiếm
                </button>
            </form>
        </div>
    </div>

    @if(session('search'))
    <div class="alert alert-info text-end">
        Tìm thấy {{ session('search') }} kết quả
    </div>
    @endif

    <!-- Bảng danh sách danh mục -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr class="text-center fw-bold">
                    <th>STT</th>
                    <th>Người dùng</th>
                    <th>Bình luận</th>
                    <th>Ngày Tạo</th>
                    <th>Trạng thái</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reviews as $review)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $review->user->name }}</td>
                    <td>{{ $review->review_text }}</td>
                    <td class="text-center">{{ $review->created_at->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 border-b border-gray-200">
                        @if($review->status == 0)
                            <span class="text-green-500">Hiện</span>
                        @else
                            <span class="text-red-500">Ẩn</span>
                        @endif
                    </td>
                    <td class="d-flex justify-content-center" style="display: flex">
                        <a href="{{ route('admin.reviews.edit', $review->id) }}" class="btn btn-sm btn-warning me-2">
                            <i class="fas fa-edit"></i> Sửa
                        </a>
                    </td>
                </tr>
                @endforeach

              
            </tbody>
        </table>
    </div>

</div>
@endsection
