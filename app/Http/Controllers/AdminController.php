<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\OrderUser;
use App\Models\Product;
use App\Models\Reviews;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.index', compact('users'));
    }

    public function rec_user()
    {
        return view('admin.index', compact('users'));
    }
    public function rec_category()
    {
        return view('admin.Category.index');
    }
    public function rec_product()
    {
        return view('admin.Product.index');
    }
    public function rec_order()
    {
        return view('admin.Order.index');
    }
    public function rec_suppliers()
    {
        return view('admin.Supplier.index');
    }
    public function rec_productType()
    {
        return view('admin.ProductType.index');
    }
    public function updateStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->keyy = $request->input('keyy');
        $user->save();

        return redirect()->route('admin.index')->with('success', 'Trạng thái người dùng đã được cập nhật.');
    }

    public function dashboard()
    {
        $totalView = Product::sum('view'); // Tính tổng lượt xem từ cột 'view' trong bảng 'products'
        $totalBrand = Brand::count();
        $totalUser = User::count();
        $totalOrder = OrderUser::count();
        $totalProduct = Product::count();
        $totalCategory = Category::count();
        $totalPrice = OrderUser::sum('total_amount');
        $totalReview = Reviews::count();

        return view('admin.index', compact(
            'totalUser',
            'totalOrder',
            'totalProduct',
            'totalCategory',
            'totalPrice',
            'totalReview',
            'totalBrand',
            'totalView' // Đảm bảo biến này được thêm vào
        ));
    }
}
