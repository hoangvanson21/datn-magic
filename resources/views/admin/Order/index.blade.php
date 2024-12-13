@extends('admin.main_admin')

@section('title', 'Danh sách đơn hàng')

@section('content')
<style>
    .collapse {
    display: none;
}

</style>
<div class="container-fluid mt-4">
    <h2 class="text-xxl font-bold mb-4 text-center">DANH SÁCH ĐƠN HÀNG</h2>

    <!-- Form tìm kiếm -->
    <div class="mb-4">
        <form action="{{ route('admin.orders.index') }}" method="GET">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="order_id" class="form-control" placeholder="Mã đơn hàng" value="{{ request('order_id') }}">
                </div>
               
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>
                </div>
            </div>
        </form>
    </div>
    

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr class="text-center">
                    <th>Mã đơn hàng</th>
                    <th>Tên sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Trạng thái</th>
                    <th>Ngày đặt hàng</th>
                    <th>Số điện thoại</th>
                    <th>Phương thức thanh toán</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    @php
                        $products = json_decode($order->products);  // Decode the products JSON
                        $totalPrice = 0;
                        $productNames = [];
                    @endphp
                    
                    @foreach($products as $product)
                        @php
                            $totalPrice += $product->price * $product->quantity;
                            $productNames[] = $product->name;
                        @endphp
                    @endforeach

                    <tr class="text-center">
                        <!-- Mã đơn hàng -->
                        <td>{{ $order->id }}</td>
                        <td>{{ !empty($productNames) ? $productNames[0] : 'N/A' }}</td>
                        <!-- Tên sản phẩm (gộp tất cả các sản phẩm) -->
                        <td>{{ number_format($totalPrice, 2) }} VNĐ</td>

                        <!-- Tổng số lượng -->
                        <td>{{ array_sum(array_column($products, 'quantity')) }}</td>

                        <!-- Trạng thái đơn hàng -->
                        <td>
                            <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <select name="status_id" class="form-select" onchange="this.form.submit()" 
                                    @if($order->status_id == 3) disabled @endif>
                                    @foreach($statuses as $status)
                                        @if($order->status_id != $status->id && $status->name != 'Giao hàng thành công' || $order->status_id == $status->id)
                                            <option value="{{ $status->id }}" {{ $order->status_id == $status->id ? 'selected' : '' }}>
                                                {{ $status->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                            </form>
                        </td>

                        <!-- Ngày đặt hàng -->
                        <td>{{ $order->created_at->format('d/m/Y') }}</td>

                        <!-- Số điện thoại -->
                        <td>{{ $order->phone }}</td>

                        <!-- Phương thức thanh toán -->
                        <td>{{ $order->payment_method }}</td>

                        <!-- Nút xem chi tiết -->
<td>
    <button class="btn btn-info btn-sm toggle-details" data-order-id="{{ $order->id }}">Xem chi tiết</button>
</td>

                    </tr>

                    <!-- Chi tiết đơn hàng -->
                    <tr id="orderDetail{{ $order->id }}" class="collapse">
                        <td colspan="9">
                            <div class="row">
                                <div class="col-md-12">
                                    <h5 class="text-center">Chi tiết đơn hàng #{{ $order->id }}</h5>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Tên sản phẩm</th>
                                                <th>Giá</th>
                                                <th>Số lượng</th>
                                                <th>Tổng giá</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($products as $product)
                                                <tr>
                                                    <td>{{ $product->name }}</td>
                                                    <td>{{ number_format($product->price, 2) }} VNĐ</td>
                                                    <td>{{ $product->quantity }}</td>
                                                    <td>{{ number_format($product->price * $product->quantity, 2) }} VNĐ</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const toggleButtons = document.querySelectorAll('.toggle-details');
    
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            const detailsRow = document.getElementById(`orderDetail${orderId}`);
            
            // Toggle hiển thị chi tiết
            detailsRow.classList.toggle('collapse');
        });
    });
});

</script>
@endsection
