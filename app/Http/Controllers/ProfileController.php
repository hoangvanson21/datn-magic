<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request)
    {
        // Lấy thông tin người dùng đã đăng nhập
        $user = $request->user();

        // Lấy thông tin địa chỉ liên quan đến người dùng
        $address = $user->address; // Giả sử có quan hệ 'address' trong mô hình User

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            'user' => $user,
            'address' => $address,  // Truyền thêm thông tin địa chỉ
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user(); // Lấy người dùng đã đăng nhập

        // Xác thực dữ liệu đầu vào
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'status' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15', // Thêm xác thực cho số điện thoại
            'street' => 'nullable|string|max:255', // Thêm xác thực cho địa chỉ
        ]);

        try {
            // Cập nhật thông tin người dùng
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'status' => $validated['status'],
            ]);

            // Cập nhật thông tin địa chỉ và số điện thoại trong bảng address
            $user->address()->update([
                'phone' => $validated['phone'] ?? $user->address->phone, // Nếu không có giá trị thì giữ nguyên
                'street' => $validated['street'] ?? $user->address->street, // Tương tự với địa chỉ
            ]);

            // Trả về phản hồi thành công
            return response()->json([
                'message' => 'Profile và thông tin địa chỉ được cập nhật thành công.',
                'count' => 1, // Thể hiện là có 1 bản ghi được cập nhật
                'uid' => $user->id,
            ]);
        } catch (\Exception $e) {
            // Xử lý lỗi
            return response()->json([
                'message' => 'Cập nhật thông tin thất bại.',
                'count' => 0, // Thể hiện thất bại
                'uid' => $user->id,
            ]);
        }
    }


    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
