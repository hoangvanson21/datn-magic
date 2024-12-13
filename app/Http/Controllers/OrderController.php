<?php

namespace App\Http\Controllers;

use App\Models\OrderStatus;
use App\Models\OrderUser;
use App\Models\Payment;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = OrderUser::query();

        if ($request->has('order_id') && $request->order_id != '') {
            $query->where('id', 'like', '%' . $request->order_id . '%');
        }

        $orders = $query->get();
        $statuses = OrderStatus::all(); // Assuming you have an OrderStatus model

        return view('admin.Order.index', compact('orders', 'statuses'));
    }

    public function updateStatus(Request $request, $id)
    {
        $user = OrderUser::findOrFail($id);

        if ($user->status_id == 3) {
            return redirect()->route('admin.orders.index')->with('error', 'Không thể thay đổi trạng thái đơn hàng đã giao thành công.');
        }
        $user->status_id = $request->input('status_id');
        $user->save();

        return redirect()->route('admin.orders.index')->with('success', 'Trạng thái đơn hàng đã được cập nhật.');
    }


    public function paymentsIndex()
    {
        $payments = Payment::all();
        $orders = OrderUser::with('products')->paginate(10); // Thay 'products' bằng quan hệ phù hợp
        $statuses = OrderStatus::all(); // Nếu bạn sử dụng trạng thái đơn hàng
        return view('admin.Orders.index', compact('orders', 'statuses', 'payments'));
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
