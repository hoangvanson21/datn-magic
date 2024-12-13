<?php

use App\Models\User;
use Inertia\Inertia;
use App\Models\Brand;
use App\Models\Colors;
use App\Models\Address;
use App\Models\Options;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Reviews;
use App\Models\Category;
use App\Models\Favorites;
use App\Models\OrderUser;
use App\Models\ProductCart;
use App\Models\ProductVariant;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\VNPayController;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\OptionController;

Route::get('/show-verify', function () {
    return Inertia::render('Auth/VerifyForm');
})->name('verify.form');
Route::post('/verify', [VerificationController::class, 'verify'])->name('auth.verify');


Route::get(
    '/newpassword',
    fn() =>
    Inertia::render('Auth/ResetPassword')
)->name('auth.newpassword');

Route::post('/api/newpassword', function (Request $request) {
    // Kiểm tra xem đây là yêu cầu reset mật khẩu hay đổi mật khẩu
    $isReset = $request->input('isReset', false);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json(['message' => 'Invalid email'], 404);
    }

    // Nếu là yêu cầu đổi mật khẩu, kiểm tra mật khẩu cũ
    if (!$isReset) {
        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['message' => 'Old password is incorrect'], 401);
        }
    }

    $user->password = Hash::make($request->password); // Sử dụng 'password' thay vì 'new_password' để trùng khớp với tên field ở frontend
    $user->save();

    return response()->json(['message' => 'Password changed successfully']);
})->name('new.password');


Route::post('/api/favorites', function (Request $request) {
    $user = Auth::user();
    if ($user) {
        $product_id = $request->productId;

        $existingWishList = Favorites::where('user_id', $user->id)
            ->where('product_id', $product_id)
            ->first();
        if ($existingWishList) {
            return response()->json(['message' => 'Sản phẩm đã có trong danh sách yêu thích'], 400);
        }
        Favorites::create([
            'user_id' => $user->id,
            'product_id' => $product_id,
        ]);

        return response()->json(['message' => 'Sản phẩm đã được thêm vào danh sách yêu thích'], 200);
    }
    return response()->json(['message' => 'Vui lòng đăng nhập để tiếp tục'], 401);
});
Route::delete('api/favorites/{productID}', function (string $productId) {
    $user = Auth::user();
    $favorite = Favorites::where('user_id', $user->id)
        ->where('product_id', $productId)
        ->first();
    if (!$favorite) {
        return response()->json(['message' => 'Không tìm thấy sản phẩm trong danh sách yêu thích.'], 404);
    }
    $favorite->delete();
    return response()->json(['message' => 'Đã xóa sản phẩm khỏi danh sách yêu thích.'], 200);
});

Route::get('verify-email/{token?}/{email?}', function ($token = null, $email = null) {
    $encryptedUserData = Cookie::get('user_data');
    if (!$encryptedUserData) {
        return Inertia::render('Auth/Login', ['error' => 'Thông tin người dùng không hợp lệ hoặc đã hết hạn.']);
    }

    $userData = decrypt($encryptedUserData);
    if ($userData['email'] !== $email) {
        return Inertia::render('Auth/Login', ['error' => 'Email không hợp lệ.']);
    }

    if ($token !== $userData['verification_code']) {
        return Inertia::render('Auth/Login', ['error' => 'Mã xác minh không đúng.']);
    }

    if (User::where('email', $userData['email'])->exists()) {
        return Inertia::render('Auth/Login', ['error' => 'Email này đã được sử dụng hoặc mã đã hết hạn.']);
    }

    $user = User::create([
        'name' => $userData['name'],
        'email' => $userData['email'],
        'password' => $userData['password'],
        'email_verified_at' => now(),
        'verification_code' => $userData['verification_code'],
    ]);
    return Inertia::render('Auth/Login', [
        'success' => 'Xác minh thành công vui lòng đăng nhập',
    ]);
});
Route::post('api/products/{id}/view', [ProductController::class, 'increaseViewCount']);




Route::get('/', function () {
    $products = Product::orderBy('id', 'desc')->limit(4)->get();
    $products_news = Product::orderByDesc('id')->get();
    $categorys = Category::orderBy('id', 'desc')->limit(6)->get();
    return Inertia::render('Welcome', [
        'products' => $products,
        'categorys' => $categorys,
        'products_news' => $products_news,

    ]);
})->name('page.home');


Route::get('/paymen/{order_code}', function ($order_code) {
    $orderId = OrderUser::where('order_code', $order_code)->value('id');
    if (!$orderId) {
        abort(404, 'Order not found');
    }
    return Inertia::render('Pay', [
        'orderId' => $orderId
    ]);
})->name('page.paymen');



Route::post('/admin/addoption', function (Request $request) {
    $option = Options::create([
        'name' => $request->option_name,
        'price' => $request->option_price,
    ]);
    return response()->json(["message" => "thêm tùy chọn thành công"], 200);
})->name('admin.options.store');


Route::post('/admin/addcolor', function (Request $request) {
    $option = Colors::create([
        'name' => $request->color_name,
    ]);
    return response()->json(["message" => "thêm color thành công"], 200);
})->name('admin.colors.store');

Route::post('/api/payment/data', function (Request $request) {
    $orderCode = $request->input('orderCode');
    $existingPayment = Payment::where('order_code', $orderCode)->first();
    if ($existingPayment) {
        return response()->json(['message' => 'Order code already exists, payment data not saved'], 400);
    }
    $paymentData = [
        'order_id' => $request->input('id'),
        'order_code' => $orderCode,
        'noidung' => $request->input('noidung'),
        'money' => $request->input('amount'),
        'status_id' => 0,
    ];
    if (Payment::create($paymentData)) {
        return response()->json(['message' => 'Payment data saved successfully'], 200);
    } else {
        return response()->json(['message' => 'Failed to save payment data'], 500);
    }
});

Route::get('/api/comments/{productId}', function ($productId) {
    $comments = Reviews::with('user')
        ->where('product_id', $productId)
        ->where('status', 1)
        ->orderBy('id', 'desc')
        ->limit(3)
        ->get()
        ->map(function ($comment) {
            return [
                'id' => $comment->id,
                'rating' => $comment->rating,
                'review_text' => $comment->review_text,
                'user_id' => $comment->user_id,
                'username' => $comment->user->name,
            ];
        });
    return response()->json($comments);
});
Route::put('/api/address/{addressid}', function (Request $request, $addressid) {
    $address = Address::find($addressid);

    if (!$address) {
        return response()->json(['message' => 'Địa chỉ không tồn tại.'], 404);
    }

    $address->name = $request->input('name');
    $address->phone = $request->input('phone');
    $address->street = $request->input('street');
    $address->save();

    return response()->json($address, 200);
});

Route::post('/api/address', function (Request $request) {
    $userId = Auth::id();

    if (!$userId) {
        return response()->json(['message' => 'User not authenticated'], 401);
    }

    $address = Address::create([
        'name' => $request->name,
        'phone' => $request->phone,
        'street' => $request->street,
        'user_id' => $userId,
    ]);

    return response()->json($address, 201);
});

Route::get('/api/address', function () {
    $user_id = Auth::id();
    if ($user_id) {
        $addresses = Address::where('user_id', $user_id)->get();
        return response()->json($addresses, 200);
    }
    return response()->json(['error' => 'Unauthorized'], 401);
});

Route::get('/api/comments/stats/{productId}', function ($productId) {
    $ratings = Reviews::select('rating')
        ->where('product_id', $productId)
        ->groupBy('rating')
        ->where('status', 1)
        ->orderBy('rating', 'desc')
        ->get()
        ->map(function ($review) {
            return [
                'stars' => $review->rating,
                'count' => Reviews::where('product_id', $productId)
                    ->where('rating', $review->rating)
                    ->where('status', 1)
                    ->count(),
            ];
        });

    return response()->json($ratings);
});


Route::post("/api/comments", function (Request $request) {
    $validatedData = $request->validate([
        'rating' => 'required|integer|between:1,5',
        'review_text' => 'required|string|max:500',
        'user_id' => 'required|exists:users,id',
        'product_id' => 'required|exists:products,id',
    ]);
    $comment = Reviews::create([
        'rating' => $validatedData['rating'],
        'review_text' => $validatedData['review_text'],
        'user_id' => $validatedData['user_id'],
        'product_id' => $validatedData['product_id'],
        'status' => 1,
    ]);

    return response()->json([
        'message' => 'Bình luận đã được gửi thành công!',
        'comment' => $comment
    ], 201);
});



Route::post('/api/order', function (Request $request) {

    $address = $request->input('address');
    $cart = $request->input('cart');
    $paymentMethod = $request->input('paymentMethod');
    $totalAmount = $request->input('totalAmount');
    $orderCode = $request->input('orderCode');

    try {
        $order = OrderUser::create([
            'user_id' => $address['user_id'],
            'name' => $address['name'],
            'phone' => $address['phone'],
            'street' => $address['street'],
            'payment_method' => $paymentMethod,
            'total_amount' => $totalAmount,
            'status_id' => 1,
            'products' => json_encode($cart),
            'order_code' => $orderCode,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đặt hàng thành công',
            'order' => $order
        ], 201);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Đã xảy ra lỗi khi tạo đơn hàng',
            'error' => $e->getMessage()
        ], 500);
    }
});

Route::get('/api/search', function (Request $request) {
    $query = $request->input('query');

    if ($query) {
        $results = Product::where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->get();
    } else {
        $results = [];
    }

    return response()->json(['results' => $results]);
})->name('search');


Route::get('/search-results', function (Request $request) {
    $query = $request->input('query');
    $results = Product::where('name', 'LIKE', "%{$query}%")->get();
    $count = $results->count();
    return Inertia::render('SearchResults', [
        'results' => $results,
        'query' => $query,
        'count' => $count
    ]);
});

Route::get('/categories/{id?}', function ($id = null) {
    if ($id) {
        $brand = Category::with('products')->where('id', $id)->firstOrFail();
        $data = [
            'brand' => $brand,
            'products' => $brand->products,
        ];
    } else {
        $data = [
            'brands' => Category::with('products')->get(),
            'products' => Product::all(),
        ];
    }
    return Inertia::render('Category', [
        'data' => $data,
    ]);
})->name('categories.show');


Route::get('/OrderHistory', function () {

    // Trả về kết quả đã map trong Inertia
    return Inertia::render('OrderHistory');
})->name('order.history');


Route::post('/api/orders', function (Request $request) {
    $user_id = Auth::id();
    if (!$user_id) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    $orderData = $request->validate([
        'cart' => 'required|array',
        'address' => 'required|array',
        'paymentMethod' => 'required|string',
        'totalAmount' => 'required|numeric',
    ]);

    $order = new OrderUser();
    $order->user_id = $user_id;
    $order->name = $orderData['address']['name'];
    $order->phone = $orderData['address']['phone'];
    $order->street = $orderData['address']['street'];
    $order->payment_method = $orderData['paymentMethod'];
    $order->total_amount = $orderData['totalAmount'];
    $order->status_id = 1;  // Trạng thái đơn hàng (ví dụ: "Chờ xử lý")
    $order->order_code = strtoupper(Str::random(10));
    $order->products = json_encode($orderData['cart']);  // Lưu danh sách sản phẩm dưới dạng JSON
    $order->save();
    DB::table('product_cart')->where('user_id', $user_id)->delete(); // L
    if ($orderData['paymentMethod'] === 'bank') {
        $paymentData = [
            'user_id' => $user_id,
            'order_id' => $order->id,
            'order_code' => $order->order_code,
            'amount' => $order->total_amount,
            'payment_method' => 'bank',
            'transaction_id' => Str::random(16),
            'transaction_date' => now(),
            'transaction_status' => 'pending',
            'ip_address' => $request->ip(),
            'note' => 'Thanh toán qua VNPay',
        ];
        DB::table('payments')->insert($paymentData);
    }
    return response()->json($order, 201);
});
Route::post('/vnpay/create-payment-link', [VNPayController::class, 'createPaymentLink']);
Route::post('/vnpay/callback', [VNPayController::class, 'callback'])->name('vnpay.callback');
Route::get('/vnpay/return', function (Request $request) {
    $vnp_Amount = $request->input('vnp_Amount');
    $vnp_BankCode = $request->input('vnp_BankCode');
    $vnp_BankTranNo = $request->input('vnp_BankTranNo');
    $vnp_OrderInfo = $request->input('vnp_OrderInfo');
    $vnp_ResponseCode = $request->input('vnp_ResponseCode');
    $vnp_SecureHash = $request->input('vnp_SecureHash');
    $vnp_TmnCode = $request->input('vnp_TmnCode');
    $vnp_TransactionNo = $request->input('vnp_TransactionNo');
    $vnp_TransactionStatus = $request->input('vnp_TransactionStatus');
    $vnp_TxnRef = $request->input('vnp_TxnRef');
    $vnp_PayDate = $request->input('vnp_PayDate');
    $secretKey = '6MX4BPMEHICWKWXZ7Q3T3Q3EJIIYTDVR';
    $hashData = http_build_query($request->except('vnp_SecureHash'));

    $secureHash = strtoupper(md5($hashData . '&' . $secretKey));

    if ($secureHash != $vnp_SecureHash) {
        if ($vnp_ResponseCode === '00' && $vnp_TransactionStatus === '00') {
            $payment = Payment::where('order_id', (int) $vnp_TxnRef)->first();

            if ($payment) {
                $payment->transaction_status = "success";
                $payment->vnp_response_code =  $vnp_ResponseCode;
                $payment->save();
            }
            // return response()->json(["message" => "Payment successful, Order ID: $vnp_TxnRef"], 200);
            // return redirect()->route('orders.history')->with('message', 'Thanh toán thành công !');
            return Inertia::render('OrderHistory', ['message' => "Thanh toan thành công"]);
        } else {
            return response()->json(["message" => "Payment failed, please try again."], 400);
        }
    } else {
        return response()->json(["message" => "Invalid response signature"], 400);
    }
})->name('vnpay.return');


Route::post('/api/vnpay', function (Request $request) {
    $client = new \GuzzleHttp\Client();
    $response = $client->post('https://sandbox.vnpayment.vn/paymentv2/vpcpay.html', [
        'form_params' => $request->all(),
    ]);
    return response()->json(json_decode($response->getBody(), true));
});



Route::get('/products', function () {
    $products = Product::with(['category:id,name', 'brand:id,name', 'ratings'])->get();
    $products = $products->map(function ($product) {
        $product->average_rating = $product->ratings->avg('rating');
        return $product;
    });
    $categories = Category::select('id', 'name')->get();
    $brands = Brand::select('id', 'name')->get();
    return Inertia::render('AllProduct', [
        'products' => $products,
        'categories' => $categories,
        'brands' => $brands,
    ]);
})->name('page.products');



Route::delete('/api/orders/{id}', function ($id) {
    $user_id = Auth::id();
    if ($user_id) {
        $order = OrderUser::where('user_id', $user_id)->where('id', $id)->first();
        if ($order) {
            Payment::where('order_id', $id)->delete();
            $order->delete();
            return response()->json(['message' => 'Đơn hàng đã được hủy thành công'], 200);
        } else {
            return response()->json(['error' => 'Đơn hàng không tồn tại hoặc bạn không có quyền hủy đơn hàng này'], 404);
        }
    }
    return response()->json(['error' => 'Unauthorized'], 200);
});

//vnpay
Route::get('/api/orders/history', function () {
    $user_id = Auth::id();

    // Lấy tất cả đơn hàng của người dùng
    $orders = OrderUser::with('orderStatus')->where('user_id', $user_id)->get();

    if ($orders->isEmpty()) {
        // Nếu không có đơn hàng nào, trả về lỗi
        return response()->json(['message' => 'Không có đơn hàng nào'], 404);
    }

    // Sử dụng map để thêm tên trạng thái vào mỗi đơn hàng
    $ordersWithStatus = $orders->map(function ($order) {
        // Lấy tên trạng thái từ quan hệ với bảng order_status
        $name_status = $order->orderStatus ? $order->orderStatus->name : 'Chưa có trạng thái';

        return [
            'id' => $order->id,
            'order_code' => $order->order_code,
            'total_amount' => $order->total_amount,
            'payment_method' => $order->payment_method,
            'created_at' => $order->created_at->format('Y-m-d H:i:s'), // Định dạng ngày giờ
            'name_status' => $name_status,
            'products' => json_decode($order->products),
        ];
    });

    // Trả về response JSON chứa danh sách đơn hàng
    return response()->json(['orders' => $ordersWithStatus], 200);
})->name('orders.history');


Route::get('/products/{id}', function ($id) {
    $product = Product::findOrFail($id);

    // Lấy các variants của sản phẩm
    $variants = ProductVariant::where('product_id', $id)->get();

    // Duyệt qua từng variant để lấy thông tin colors và options
    $variantData = $variants->map(function ($variant) {
        // Lấy thông tin color
        $color = Colors::find($variant->color_id);

        // Lấy thông tin option
        $option = Options::find($variant->option_id);

        return [
            'variant_id' => $variant->id,
            'variant_price' => $variant->variant_price,
            'variant_quantity' => $variant->variant_quantity,
            'color' => $color ? [
                'id' => $color->id,
                'name' => $color->name,
            ] : null,
            'option' => $option ? [
                'id' => $option->id,
                'ram' => $option->ram,
                'rom' => $option->rom,
            ] : null,
        ];
    });

    $productData = [
        'id' => $product->id,
        'name' => $product->name,
        'description' => $product->description,
        'base_price' => $product->price,
        'dis_price' => $product->dis_price,
        'images' => json_decode($product->images, true) ?? [], // Giải mã hình ảnh
        'variants' => $variantData, // Danh sách variants
    ];

    return Inertia::render('Details', ['productData' => $productData]);
})->name('products.show');




Route::get('/wishlist', function () {
    $user_id = Auth::id();
    if ($user_id) {
        $wishlists = Favorites::with('product')->where('user_id', $user_id)->get();

        return Inertia::render('Wishlist', ['wishlists' => $wishlists]);
    } else {
        return redirect()->route('login');
    }
})->name('wishlist');









Route::get('/list/category', function () {
    $categories = Category::all();
    return response()->json(['categories' => $categories], 200);
})->name('categories.list');

// Đăng xuất
Route::get('/checkout', function () {
    return Inertia::render('Checkout');
});

// =========About===========
Route::get('/about', function () {
    return Inertia::render('About');
})->name('page.about');

// =========Support===========
Route::get('/support', function () {
    return Inertia::render('Support');
})->name('page.support');
// =========Contact===========
Route::get('/contact', function () {
    return Inertia::render('Contact');
})->name('page.contact');

// ====Cart=====
Route::get('/cart', function () {
    $userId = Auth::id();
    $cartItems = ProductCart::with(['product:id,images,name', 'option:id,ram,rom',  'color:id,name'])
        ->where('user_id', $userId)
        ->get()
        ->map(function ($cartItem) {
            return [
                'id' => $cartItem->id,
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->price,
                'product' => $cartItem->product,
                'option_name' => $cartItem->option ? $cartItem->option->ram . 'GB / ' . $cartItem->option->rom . 'GB' : null,
                'color_name' => $cartItem->color ? $cartItem->color->name : null,
            ];
        });
    return Inertia::render('CartList', [
        'cartItems' => $cartItems
    ]);
})->name('cart');










Route::post('/products', function (Request $request) {
    $product = Product::find($request->Product_ID);
    if (!$product) {
        return response()->json(['error' => 'Sản phẩm không tồn tại'], 404);
    }

    $ProductOptionID = $request->Option_ID;
    $ProductColorID = $request->Color_ID;
    $ProductQuantity = $request->quantity;
    $ProductTotal = $request->totalPrice;
    $existingCartItem = ProductCart::where('user_id', Auth::id())
        ->where('product_id', $product->id)
        ->first();
    if ($existingCartItem) {
        $matchingItem = ProductCart::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->where('option_id', $ProductOptionID)
            ->where('color_id', $ProductColorID)
            ->first();
        if ($matchingItem) {
            $matchingItem->quantity += $ProductQuantity;
            $matchingItem->save();
        } else {
            ProductCart::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'quantity' => $ProductQuantity,
                'price' => $ProductTotal,
                'option_id' => $ProductOptionID,
                'color_id' => $ProductColorID,
            ]);
        }
    } else {
        ProductCart::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'quantity' => $ProductQuantity,
            'price' => $ProductTotal,
            'option_id' => $ProductOptionID,
            'color_id' => $ProductColorID,
        ]);
    }
    return response()->json(['success' => 'Thêm vào giỏ hàng thành công'], 200);
});



// ===Cật nhật số lượng====
Route::post('/api/cart/quantity', function (Request $request) {
    $cartItem = ProductCart::find($request->id);
    if ($cartItem) {
        $cartItem->quantity = $request->quantity;
        $cartItem->save();
        return response()->json([
            'quantity' => $cartItem->quantity,
        ], 200);
    }
    return response()->json([
        'message' => 'Sản phẩm không tìm thấy.',
    ], 404);
});



Route::post('/api/cart/add', function (Request $request) {
    if (!Auth::check()) {
        return response()->json(['error' => 'Bạn cần đăng nhập để thêm vào giỏ hàng.'], 401);
    }

    $product = Product::find($request->product_id);
    if (!$product) {
        return response()->json(['error' => 'Sản phẩm không tồn tại.'], 404);
    }

    $userId = Auth::id();
    $optionId = $request->input('option_id');
    $colorId = $request->input('color_id');
    $quantity = $request->input('quantity', 1);

    // Lấy giá sản phẩm (bao gồm giá base + giá option + giá color)
    $basePrice = $product->price;
    $variantPrice = 0;

    // Kiểm tra và lấy giá của option nếu có
    if ($optionId && $colorId) {
        $variant = ProductVariant::where('product_id', $product->id)
            ->where('option_id', $optionId)
            ->where('color_id', $colorId)
            ->first();

        if ($variant) {
            $variantPrice = $variant->variant_price; // Sử dụng giá variant
        } else {
            $variantPrice = $basePrice; // Nếu không có variant, dùng giá base
        }
    } else {
        $variantPrice = $basePrice; // Nếu không có tùy chọn, dùng giá base
    }
    $totalPrice = ($variantPrice) * $quantity;

    // Kiểm tra sản phẩm đã tồn tại trong giỏ hàng chưa
    $cartItem = ProductCart::where('user_id', $userId)
        ->where('product_id', $product->id)
        ->where('option_id', $optionId)
        ->where('color_id', $colorId)
        ->first();

    if ($cartItem) {
        // Nếu đã có, cập nhật số lượng và giá
        $cartItem->quantity += $quantity;
        $cartItem->price = $totalPrice;
        $cartItem->save();
    } else {
        // Nếu chưa có, thêm mới vào giỏ hàng
        ProductCart::create([
            'user_id' => $userId,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'price' => $totalPrice,
            'option_id' => $optionId,
            'color_id' => $colorId,
        ]);
    }

    return response()->json(['success' => 'Sản phẩm đã được thêm vào giỏ hàng.'], 200);
});




// =======Xóa sản phẩm khỏi giỏ==
Route::post('/api/cart/remove', function (Request $request) {
    $ids = $request->input('ids');
    if ($ids && is_array($ids)) {
        $deletedCount = ProductCart::whereIn('id', $ids)->delete();
        return response()->json(['success' => true, 'message' => 'Xóa sản phẩm thành công'], 200);
    }
    return response()->json(['success' => false, 'message' => 'Xóa sản phẩm thất bại'], 400);
});




Route::post('api/user/update', function (Request $request) {
    $user = User::find($request->id);
    $user->name = $request->name;
    $user->email = $request->email;
    $user->status = $request->status;
    $user->save();
    return response()->json(['message' => 'Cập nhất thành công'], 200);
});




Route::get('/api/count/cart', function (Request $request) {
    $userId = Auth::id();
    $cartCount = ProductCart::where('user_id', $userId)->count();
    return response()->json(['count' => $cartCount, "uid" => $userId], 200);
});


Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('/products/show', [ProductController::class, 'show']);


Route::prefix('admin')->name('admin.')->middleware(['admin'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::get('/', [AdminController::class, 'dashboard'])->name('index');
    Route::get('/admin', [AdminController::class, 'index'])->name('admin');

    Route::resource('colors', ColorController::class);
    Route::resource('options', OptionController::class);

    Route::get('/users', [AdminController::class, 'rec_user'])->name('users');
    Route::resource('users', AdminController::class);

    Route::get('/categories', [AdminController::class, 'rec_category'])->name('categories');
    Route::resource('categories', CategoryController::class);

    Route::get('/products', [AdminController::class, 'rec_product'])->name('products');
    Route::resource('products', ProductController::class);
    Route::get('products/{id}/restore', [ProductController::class, 'restore_product'])->name('products.restore');
    Route::delete('products/{id}/force-delete', [ProductController::class, 'force_delete_product'])->name('products.force_delete');
    Route::get('/trash_product', [ProductController::class, 'trash_product'])->name('trash_product');

    Route::get('/orders', [AdminController::class, 'rec_order'])->name('orders');
    Route::get('/orders/', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/payment/', [OrderController::class, 'paymentsIndex'])->name('payments.index');
    Route::patch('/orders/{id}/updateStatus', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');

    Route::get('/brands', [BrandController::class, 'rec_brands'])->name('brands');
    Route::resource('brands', BrandController::class);
    Route::get('brands/{id}/restore', [BrandController::class, 'restore_brand'])->name('restore_brand');

    Route::get('/trash_brand', [BrandController::class, 'trash_brand'])->name('trash_brands');


    Route::resource('reviews', ReviewController::class);
    Route::patch('reviews/{id}/toggle-status', [ReviewController::class, 'toggleStatus'])->name('reviews.toggleStatus');
    Route::get('reviews/{id}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('reviews/{id}', [ReviewController::class, 'update'])->name('reviews.update');

    Route::patch('/users/{id}/update-status', [AdminController::class, 'updateStatus'])->name('users.updateStatus');
});

require __DIR__ . '/auth.php';
