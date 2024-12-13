<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = Brand::join('users', 'brands.user_id', '=', 'users.id')
            ->select('brands.*', 'users.status as status');

        if ($search) {
            $brands = $query->where('brands.name', 'like', "%{$search}%")->paginate(10);
            $request->session()->flash('search', $brands->total());  // Lưu kết quả tìm kiếm vào session
        } else {
            $brands = $query->paginate(10);
        }

        return view('admin.Brands.index', compact('brands'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.Brands.create');
    }

    /**
     * Store a newly created resource in storage.

     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:225',
            'description' => 'required|string|max:255',
        ], [
            'name.required' => 'Tên thương hiệu là bắt buộc.',
            'name.string' => 'Tên thương hiệu phải là chuỗi ký tự.',
            'name.max' => 'Tên thương hiệu không được vượt quá 225 ký tự.',
            'description.required' => 'Mô tả thương hiệu là bắt buộc.',
            'description.string' => 'Mô tả thương hiệu phải là chuỗi ký tự.',
            'description.max' => 'Mô tả thương hiệu không được vượt quá 255 ký tự.',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();
        Brand::create($data);
        return redirect()->route('admin.brands.index')->with('success', 'Tạo thương hiệu thành công !');
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
        $brand = Brand::findorFail($id);
        return view('admin.Brands.edit', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:225',
            'description' => 'required|string|max:255',
        ]);

        $brand = Brand::findorFail($id);
        $brand->update($request->all());
        return redirect()->route('admin.brands.index')->with('success', 'Cập nhật thương hiệu thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $brands = Brand::findorFail($id);
        $brands->delete();
        return redirect()->route('admin.brands.index')->with('success', 'Xóa thương hiệu thành công!');
    }
    public function trash_brand()
    {
        $brands = Brand::onlyTrashed()->paginate(10);
        return view('admin.Brands.trash', compact('brands'));
    }
    public function restore_brand($id)
    {
        $brand = Brand::withTrashed()->where('id', $id)->first();
        $brand->restore();
        return redirect()->route('admin.trash_brands')->with('success', 'Khôi phục thương hiệu thành công!');
    }
}
