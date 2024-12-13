<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Colors;
use App\Models\Options;
use App\Models\Product;
use App\Models\Category;
use App\Models\Discount;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = Product::with('colors', 'options');

        if ($search) {
            // Thực hiện tìm kiếm theo tên sản phẩm
            $products = $query->where('name', 'like', "%{$search}%")->paginate(10);
            $request->session()->flash('search', $products->total()); // Lưu kết quả tìm kiếm vào session
        } else {
            $products = $query->paginate(10);
        }

        return view('admin.Product.index', compact('products'));
    }

    public function create()
    {
        $brands = Brand::all();
        $categories = Category::all();
        $discounts = Discount::all();
        $options = Options::all();
        $colors = Colors::all();
        return view('admin.Product.create', compact('brands', 'categories', 'discounts', 'options', 'colors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'price' => 'required|numeric|gt:0',
            'dis_price' => 'required|numeric|lt:price', // Giá khuyến mãi phải nhỏ hơn giá gốc
            'quantity' => 'required|integer|min:1',
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'variants' => 'nullable|array',
            'variants.*.color_id' => 'required_with:variants|exists:colors,id',
            'variants.*.option_id' => 'required_with:variants|exists:options,id',
            'variants.*.variant_price' => 'required_with:variants|numeric|gt:0',
            'variants.*.variant_quantity' => 'required_with:variants|integer|min:0',
        ], [
            'name.required' => 'Tên sản phẩm là bắt buộc.',
            'price.required' => 'Giá sản phẩm là bắt buộc.',
            'price.numeric' => 'Giá phải là số.',
            'price.gt' => 'Giá phải lớn hơn 0.',
            'dis_price.lt' => 'Giá khuyến mãi phải nhỏ hơn giá gốc.',
            'quantity.required' => 'Số lượng sản phẩm là bắt buộc.',
            'quantity.min' => 'Số lượng phải lớn hơn hoặc bằng 1.',
            'brand_id.exists' => 'Thương hiệu không hợp lệ.',
            'category_id.exists' => 'Danh mục không hợp lệ.',
            'images.*.image' => 'Mỗi tệp tải lên phải là hình ảnh.',
            'variants.*.color_id.exists' => 'Màu sắc không hợp lệ.',
            'variants.*.variant_price.gt' => 'Giá của biến thể phải lớn hơn 0.',
        ]);

        // Xử lý ảnh
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('uploads/products', 'public');
                $images[] = $path;
            }
        }

        // Lưu sản phẩm chính
        $product = Product::create([
            'name' => $validated['name'],
            'price' => $validated['price'],
            'dis_price' => $validated['dis_price'],
            'quantity' => $validated['quantity'],
            'brand_id' => $validated['brand_id'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'],
            'images' => json_encode($images),
        ]);
        // Lưu các biến thể
        if (!empty($validated['variants'])) {
            foreach ($validated['variants'] as $variant) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'color_id' => $variant['color_id'],
                    'option_id' => $variant['option_id'],
                    'variant_price' => $variant['variant_price'],
                    'variant_quantity' => $variant['variant_quantity'],
                ]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được thêm thành công.');
    }




    public function edit($id)
    {
        $product = Product::with('options', 'colors')->find($id);

        if (!$product) {
            return redirect()->route('admin.products.index')->with('error', 'Sản phẩm không tồn tại.');
        }

        $brands = Brand::all();
        $categories = Category::all();
        $discounts = Discount::all();
        $options = Options::all();
        $colors = Colors::all();

        return view('admin.Product.edit', compact('product', 'brands', 'categories', 'discounts', 'options', 'colors'));
    }


    public function update(Request $request, string $id)
    {
        // Xác thực dữ liệu đầu vào
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|numeric',
            'dis_price' => 'required|numeric|lt:price', // Giá khuyến mãi phải nhỏ hơn giá gốc
            'quantity' => 'required|integer',
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'status_id' => 'nullable|exists:statuses,id',
            'discount_id' => 'nullable|exists:discounts,id',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'options_id' => 'nullable|array',
            'options_id.*' => 'exists:options,id',
            'colors_id' => 'nullable|array',
            'colors_id.*' => 'exists:colors,id',
        ], [
            'name.required' => 'Tên sản phẩm là bắt buộc.',
            'price.required' => 'Giá sản phẩm là bắt buộc.',
            'price.numeric' => 'Giá phải là số.',
            'price.gt' => 'Giá phải lớn hơn 0.',
            'dis_price.lt' => 'Giá khuyến mãi phải nhỏ hơn giá gốc.',
            'quantity.required' => 'Số lượng sản phẩm là bắt buộc.',
            'quantity.min' => 'Số lượng phải lớn hơn hoặc bằng 1.',
            'brand_id.exists' => 'Thương hiệu không hợp lệ.',
            'category_id.exists' => 'Danh mục không hợp lệ.',
            'images.*.image' => 'Mỗi tệp tải lên phải là hình ảnh.',
            'variants.*.color_id.exists' => 'Màu sắc không hợp lệ.',
            'variants.*.variant_price.gt' => 'Giá của biến thể phải lớn hơn 0.',
        ]);

        $product = Product::findOrFail($id);

        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imageName = Str::random(40) . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('upload'), $imageName);
                $imagePaths[] = 'upload/' . $imageName;
            }
            $validatedData['images'] = json_encode($imagePaths);
        }

        $product->update($validatedData);

        if ($request->has('options_id') && $request->has('colors_id')) {
            $product->variants()->delete();

            foreach ($request->colors_id as $colorId) {
                foreach ($request->options_id as $optionId) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'color_id' => $colorId,
                        'option_id' => $optionId,
                        'variant_price' => $request->input('variant_price')[$colorId][$optionId] ?? $product->price, // Giá variant có thể được điều chỉnh
                        'quantity' => $request->input('variant_quantity')[$colorId][$optionId] ?? $product->quantity, // Số lượng variant
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm "' . $product->name . '" thành công.');
    }



    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $optionsIds = json_decode($product->options_id);
        if (!empty($optionsIds)) {
            Options::whereIn('id', $optionsIds)->delete();
        }
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được xóa vào thùng rác.');
    }
    public function trash_product()
    {
        $products = Product::onlyTrashed()->paginate(10);
        return view('admin.Product.trash', compact('products'));
    }
    public function restore_product($id)
    {
        $product = Product::withTrashed()->where('id', $id)->first();

        if (!$product) {
            return redirect()->route('admin.trash_product')->with('error', 'Sản phẩm không tồn tại.');
        }

        $product->restore();
        return redirect()->route('admin.trash_product')->with('success', 'Sản phẩm đã được khôi phục.');
    }
    public function increaseViewCount($id)
    {
        $product = Product::findorFail($id);

        if ($product) {
            $product->increment('view'); // Tăng giá trị của cột `view` trong database
            return response()->json(['success' => true, 'view' => $product->view]);
        }

        return response()->json(['success' => false, 'message' => 'Product not found'], 404);
    }
}
