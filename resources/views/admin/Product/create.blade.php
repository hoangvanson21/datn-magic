@extends('admin.main_admin')

@section('title', 'Thêm Sản Phẩm')
<style>
    #preview_images{
        display: flex
    }
</style>
@section('content')
<div class="container-fluid my-5">
    <h2 class="text-center mb-4">Thêm Sản Phẩm</h2>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

        <div class="row">
            <!-- Tên sản phẩm -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name" class="control-label">Tên sản phẩm</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required class="form-control">
                </div>
            </div>
<!-- Số lượng sản phẩm -->
<div class="col-md-6">
    <div class="form-group">
        <label for="quantity" class="control-label">Số lượng sản phẩm</label>
        <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" required class="form-control">
    </div>
</div>
            <!-- Giá sản phẩm -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="price" class="control-label">Giá sản phẩm </label>
                    <input type="number" name="price" id="price" value="{{ old('price') }}" step="0.01" required class="form-control">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="price" class="control-label">Giá khuyến mãi</label>
                    <input type="number" name="dis_price" id="dis_price" value="{{ old('dis_price') }}" step="0.01" required class="form-control">
                </div>
            </div>

            

            <!-- Thương hiệu -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="brand_id" class="control-label">Thương hiệu</label>
                    <select name="brand_id" id="brand_id" class="form-control">
                        <option value="">Chọn thương hiệu</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
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
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Mô tả sản phẩm -->
            <div class="col-md-12">
                <div class="form-group">
                    <label for="description" class="control-label">Mô tả sản phẩm</label>
                    <textarea name="description" id="description" rows="4" required class="form-control">{{ old('description') }}</textarea>
                </div>
            </div>

            <!-- Hình ảnh sản phẩm -->
            <div class="col-md-12">
                <div class="form-group">
                    <label for="images" class="control-label">Hình ảnh sản phẩm</label>
                    <input type="file" id="fileInput" name="images[]" required multiple class="form-control">
                </div>
                <div id="preview_images" class="d-flex flex-wrap gap-2 mt-3">
                    <!-- Các hình ảnh đã tải lên sẽ được thêm vào đây -->
                </div>
            </div>
        </div>

        <!-- Tùy chọn và màu sắc -->
        <div id="form-options"></div>

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
<script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
<script>
     CKEDITOR.replace('description');

// Xử lý xem trước hình ảnh
document.getElementById('fileInput').addEventListener('change', function(event) {
    const previewContainer = document.getElementById('preview_images');
    previewContainer.innerHTML = ''; 

    const files = event.target.files;

    if (files.length > 5) {
        alert('Bạn chỉ được chọn tối đa 5 ảnh.');
        return;
    }

    for (const file of files) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.width = '100px';
            img.style.height = '100px';
            img.style.objectFit = 'cover';
            img.style.marginRight = '10px';
            img.style.border = '1px solid #ccc';
            img.style.borderRadius = '5px';
            previewContainer.appendChild(img);
        };
        reader.readAsDataURL(file);
    }
});
</script>
<script>
    let variantCounter = 0;

document.getElementById('add-variant-btn').addEventListener('click', function() {
    const formOptions = document.getElementById('form-options');
    const newVariantDiv = document.createElement('div');
    newVariantDiv.classList.add('form-option');
    newVariantDiv.innerHTML = `
        <div class="row border p-3 mb-3">
            <div class="col-md-3">
                <label for="variants[${variantCounter}][color_id]">Màu sắc</label>
                <select name="variants[${variantCounter}][color_id]" class="form-control">
                    <!-- Render các màu sắc từ Blade -->
                    @foreach ($colors as $color)
                        <option value="{{ $color->id }}">{{ $color->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="variants[${variantCounter}][option_id]">Thông số</label>
                <select name="variants[${variantCounter}][option_id]" class="form-control">
                    <!-- Render các options từ Blade -->
                    @foreach ($options as $option)
                        <option value="{{ $option->id }}">{{ $option->ram }} GB / {{ $option->rom }} GB</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="variants[${variantCounter}][variant_price]">Giá</label>
                <input type="number" name="variants[${variantCounter}][variant_price]" class="form-control" step="0.01" required>
            </div>
            <div class="col-md-3">
                <label for="variants[${variantCounter}][variant_quantity]">Số lượng</label>
                <input type="number" name="variants[${variantCounter}][variant_quantity]" class="form-control" step="1" required>
            </div>
        </div>
    `;
    formOptions.appendChild(newVariantDiv);
    variantCounter++;
});

</script>
@endsection
