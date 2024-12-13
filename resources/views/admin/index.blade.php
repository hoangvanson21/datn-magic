@extends('admin.main_admin')

@section('title', 'Thống kê quản trị')

@section('content')
<style>
    .hover\:bg-blue-600:hover {
    background-color: #2563eb;
}

.hover\:bg-green-600:hover {
    background-color: #16a34a;
}

.hover\:bg-yellow-600:hover {
    background-color: #d97706;
}

.hover\:bg-red-600:hover {
    background-color: #dc2626;
}

.hover\:bg-gray-700:hover {
    background-color: #374151;
}

.hover\:bg-purple-600:hover {
    background-color: #7c3aed;
}

.hover\:bg-indigo-600:hover {
    background-color: #4f46e5;
}

</style>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <!-- Tổng số người dùng -->
    <a href="{{ route('admin.users.index') }}" class="bg-blue-500 text-white p-4 rounded-lg shadow-md block hover:bg-blue-600 transition duration-300 ease-in-out">
        <h3 class="text-2xl">{{ $totalUser }}</h3>
        <p>Người dùng</p>
    </a>

    <!-- Tổng số sản phẩm -->
    <a href="{{ route('admin.products.index') }}" class="bg-green-500 text-white p-4 rounded-lg shadow-md block hover:bg-green-600 transition duration-300 ease-in-out">
        <h3 class="text-2xl">{{ $totalProduct }}</h3>
        <p>Sản phẩm</p>
    </a>

    <!-- Tổng số đơn hàng -->
    <a href="{{ route('admin.orders.index') }}" class="bg-blue-400 text-white p-4 rounded-lg shadow-md block hover:bg-blue-500 transition duration-300 ease-in-out">
        <h3 class="text-2xl">{{ $totalOrder }}</h3>
        <p>Đơn hàng</p>
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <!-- Tổng danh mục -->
    <a href="{{ route('admin.categories.index') }}" class="bg-yellow-500 text-white p-4 rounded-lg shadow-md block hover:bg-yellow-600 transition duration-300 ease-in-out">
        <h3 class="text-2xl">{{ $totalCategory }}</h3>
        <p>Danh mục</p>
    </a>

    <!-- Tổng doanh thu -->
    <a href="#" class="bg-red-500 text-white p-4 rounded-lg shadow-md block hover:bg-red-600 transition duration-300 ease-in-out">
        <h3 class="text-2xl">{{ number_format($totalPrice, 0, ',', '.') }} VNĐ</h3>
        <p>Doanh thu</p>
    </a>

    <!-- Tổng lượt đánh giá -->
    <a href="{{ route('admin.reviews.index') }}" class="bg-gray-600 text-white p-4 rounded-lg shadow-md block hover:bg-gray-700 transition duration-300 ease-in-out">
        <h3 class="text-2xl">{{ $totalReview }}</h3>
        <p>Lượt đánh giá</p>
    </a>

    <!-- Tổng thương hiệu -->
    <a href="{{ route('admin.brands.index') }}" class="bg-purple-500 text-white p-4 rounded-lg shadow-md block hover:bg-purple-600 transition duration-300 ease-in-out">
        <h3 class="text-2xl">{{ $totalBrand }}</h3>
        <p>Thương hiệu</p>
    </a>

    <!-- Tổng lượt xem -->
    <a href="#" class="bg-indigo-500 text-white p-4 rounded-lg shadow-md block hover:bg-indigo-600 transition duration-300 ease-in-out">
        <h3 class="text-2xl">{{ $totalView }}</h3>
        <p>Tổng lượt xem sản phẩm</p>
    </a>
</div>

@endsection
