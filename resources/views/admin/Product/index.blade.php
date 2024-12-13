@extends('admin.main_admin')

@section('title', 'Danh sách Sản Phẩm')

@section('content')
<div class="container-fluid mt-4">
    <h2 class="text-xxl font-bold mb-4 text-center">DANH SÁCH SẢN PHẨM</h2>

    <!-- Nút thêm và thùng rác -->
    <div class="row mb-3">
        <div class="col-md-6">
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Thêm Sản Phẩm
            </a>
            <a href="{{ route('admin.trash_product') }}" class="btn btn-success ms-2">
                <i class="fas fa-trash"></i> Thùng Rác
            </a>
        </div>

        <!-- Form tìm kiếm -->
        <div class="col-md-6">
            <form action="{{ route('admin.products.index') }}" method="GET" class="d-flex justify-content-end" style="display: flex">
                <input type="text" name="search" placeholder="Tìm sản phẩm" class="form-control me-2" value="{{ request()->get('search') }}">
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

    <!-- Bảng danh sách sản phẩm -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr class="text-center fw-bold">
                    <th>STT</th>
                    <th>Tên Sản Phẩm</th>
                    <th>Hình Ảnh</th>
                    <th>Giá</th>
                    <th>Tồn Kho</th>
                    <th>Trạng Thái</th>
                    <th>Ngày Tạo</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $index => $product)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $product->name }}</td>
                    <td class="text-center">
                        @if($product->images)
                            @php
                                $images = json_decode($product->images);  // Giải mã JSON chứa danh sách hình ảnh
                            @endphp
                            @if(!empty($images) && isset($images[0]))
                            <img src="{{ asset('storage/' . $images[0]) }}" alt="{{ $product->name }}" width="80px">
                        @else
                            <span>Chưa có hình ảnh</span>
                        @endif
                        @else
        <span>Chưa có hình ảnh</span>
    @endif
                    </td>
                    
                    <td class="text-center">{{ number_format($product->price, 2) }} VNĐ</td>
                    <td class="text-center">{{ $product->quantity }}</td>
                    <td class="text-center">
                        {{ $product->quantity > 0 ? 'Còn hàng' : 'Hết hàng' }}
                    </td>
                    <td class="text-center">{{ $product->created_at->format('d/m/Y') }}</td>
                    <td class="d-flex justify-content-center" style="display: flex">
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-warning me-2">
                            <i class="fas fa-edit"></i> Sửa
                        </a>
                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')">
                                <i class="fas fa-trash"></i> Xóa
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach

                @if($products->isEmpty())
                <tr>
                    <td colspan="8" class="text-center text-muted">Không có sản phẩm nào.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Phân trang -->
    <div class="d-flex justify-content-center mt-3">
        {{ $products->links('pagination::bootstrap-4') }}
    </div>
</div>
@endsection
