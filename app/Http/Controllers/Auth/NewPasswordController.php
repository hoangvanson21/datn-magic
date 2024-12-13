<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('Auth/ResetPassword', [
            'email' => $request->email,
            'token' => $request->route('token'),
            'isReset' => true,
        ]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Kiểm tra trạng thái của isReset
        $isReset = $request->input('isReset', false);

        // Xác định quy tắc validate dựa trên trạng thái của isReset
        $validationRules = [
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];

        if (!$isReset) {
            $validationRules['old_password'] = 'required';
        }

        // Thực hiện validate
        $request->validate($validationRules);

        // Nếu là reset mật khẩu, không kiểm tra mật khẩu cũ
        if ($isReset) {
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user) use ($request) {
                    // Cập nhật mật khẩu người dùng
                    $user->forceFill([
                        'password' => Hash::make($request->password),
                        'remember_token' => Str::random(60),
                    ])->save();

                    event(new PasswordReset($user));
                }
            );

            if ($status == Password::PASSWORD_RESET) {
                return redirect()->route('login')->with('status', __($status));
            }

            // Nếu có lỗi, ném ra thông báo lỗi
            throw ValidationException::withMessages([
                'email' => [trans($status)],
            ]);
        } else {
            // Đổi mật khẩu yêu cầu kiểm tra mật khẩu cũ
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->old_password, $user->password)) {
                throw ValidationException::withMessages([
                    'old_password' => ['Mật khẩu cũ không đúng'],
                ]);
            }

            // Cập nhật mật khẩu người dùng
            $user->forceFill([
                'password' => Hash::make($request->password),
                'remember_token' => Str::random(60),
            ])->save();

            return redirect()->route('login')->with('status', 'Đổi mật khẩu thành công');
        }
    }
}
