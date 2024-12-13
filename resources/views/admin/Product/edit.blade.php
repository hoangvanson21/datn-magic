@extends('admin.main_admin')

@section('title', 'Chỉnh Sửa Sản Phẩm')

@section('content')
<div class="container-fluid my-5">
    <h2 class="text-center mb-4">Chỉnh Sửa Sản Phẩm</h2>

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT') <!-- Phương thức PUT để cập nhật -->

        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

        <div class="row">
            <!-- Tên sản phẩm -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name" class="control-label">Tên sản phẩm</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required class="form-control">
                </div>
            </div>
 <!-- Số lượng sản phẩm -->
 <div class="col-md-6">
    <div class="form-group">
        <label for="quantity" class="control-label">Số lượng sản phẩm</label>
        <input type="number" name="quantity" id="quantity" value="{{ old('quantity', $product->quantity) }}" required class="form-control">
    </div>
</div>
<div class="col-md-6">
    <div class="form-group">
        <label for="dis_price" class="control-label">Giá sản phẩm </label>
        <input type="number" name="dis_price" id="dis_price" value="{{ old('dis_price', $product->dis_price) }}" step="0.01" required class="form-control">
    </div>
</div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="price" class="control-label"> Giá khuyến mãi</label>
                    <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" step="0.01" required class="form-control">
                </div>
            </div>
            
            <!-- Thương hiệu -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="brand_id" class="control-label">Thương hiệu</label>
                    <select name="brand_id" id="brand_id" class="form-control">
                        <option value="">Chọn thương hiệu</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Danh mục sản phẩm -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="category_id" class="control-label">Danh mục sản phẩm</label>
                    <select name="category_id" id="category_id" class="form-control">
                        <option value="">Chọn danh mục</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Mô tả sản phẩm -->
            <div class="col-md-12">
                <div class="form-group">
                    <label for="description" class="control-label">Mô tả sản phẩm</label>
                    <textarea name="description" id="description" rows="4" required class="form-control">{{ old('description', $product->description) }}</textarea>
                </div>
            </div>

            <!-- Hình ảnh sản phẩm -->
            <div class="col-md-12">
                <div class="form-group">
                    <label for="images" class="control-label">Hình ảnh sản phẩm</label>
                    <input type="file" id="fileInput" name="images[]" multiple accept="image/*" class="form-control">
                </div>
                <div id="preview_images" class="d-flex flex-wrap gap-2 mt-3">
                    <!-- Các hình ảnh hiện có sẽ được hiển thị ở đây -->
                    {{-- @foreach($product->images as $image)
                        <img src="{{ asset('storage/' . $image->path) }}" style="width: 80px; height: 80px; margin-right: 10px;">
                    @endforeach --}}
                </div>
            </div>
        </div>

        <!-- Tùy chọn và màu sắc -->
<div id="form-options">
    @foreach($product->variants as $variant)
        <div class="row form-option">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="color" class="control-label">Chọn màu sắc</label>
                    <select name="colors_id[]" class="form-control" required>
                        @foreach ($colors as $color)
                            <option value="{{ $color->id }}" {{ old('colors_id[]', $variant->color_id) == $color->id ? 'selected' : '' }}>{{ $color->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="ram_rom" class="control-label">Chọn thông số</label>
                    <select name="options_id[]" class="form-control" required>
                        @foreach ($options as $option)
                            <option value="{{ $option->id }}" {{ old('options_id[]', $variant->option_id) == $option->id ? 'selected' : '' }}>
                                {{ $option->ram }} / {{ $option->rom }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="variant_price" class="control-label">Giá cho tùy chọn</label>
                    <input type="number" name="variant_price[]" class="form-control" value="{{ old('variant_price[]', $variant->variant_price) }}" step="0.01">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="variant_quantity" class="control-label">Số lượng</label>
                    <input type="number" name="variant_quantity[]" class="form-control" value="{{ old('variant_quantity[]', $variant->variant_quantity) }}" step="0.01">
                </div>
            </div>
        </div>
    @endforeach
</div>


        <div class="form-group">
            <button type="button" id="add-variant-btn" class="btn btn-primary">Thêm tùy chọn</button>
        </div>

        <!-- Submit and Reset Buttons -->
        <div class="form-group">
            <button type="reset" class="btn btn-danger">Hủy bỏ</button>
            <button type="submit" class="btn btn-success">Lưu sản phẩm</button>
        </div>
    </form>
</div>

<script>
    // Tạo preview hình ảnh khi người dùng chọn ảnh
    document.getElementById('fileInput').addEventListener('change', function(event) {
        const previewContainer = document.getElementById('preview_images');
        previewContainer.innerHTML = ''; // Clear previous previews

        const files = event.target.files;
        for (let i = 0; i < files.length; i++) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '80px';
                img.style.height = '80px';
                img.style.marginRight = '10px';
                previewContainer.appendChild(img);
            };
            reader.readAsDataURL(files[i]);
        }
    });

    // Xử lý thêm tùy chọn khi người dùng click vào nút "Thêm tùy chọn"
    document.getElementById('add-variant-btn').addEventListener('click', function() {
        const formOptions = document.getElementById('form-options');
        const newVariantDiv = document.createElement('div');
        newVariantDiv.classList.add('form-option');
        newVariantDiv.innerHTML = ` 
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="color" class="control-label">Chọn màu sắc</label>
                        <select name="colors_id[]" class="form-control" required>
                            @foreach ($colors as $color)
                                <option value="{{ $color->id }}">{{ $color->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="ram_rom" class="control-label">Chọn thông số</label>
                        <select name="options_id[]" class="form-control" required>
                            @foreach ($options as $option)
                                <option value="{{ $option->id }}">{{ $option->ram }} / {{ $option->rom }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="variant_price" class="control-label">Giá cho tùy chọn</label>
                        <input type="number" name="variant_price[]" class="form-control" step="0.01">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="variant_quantity" class="control-label">Số lượng</label>
                        <input type="number" name="quantity[]" class="form-control">
                    </div>
                </div>
            </div>
        `;
        formOptions.appendChild(newVariantDiv);
    });
</script>

@endsection
