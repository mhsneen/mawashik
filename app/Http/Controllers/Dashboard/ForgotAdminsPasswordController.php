<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Http\Requests\Dashboard\ForgotpasswordRequest;
use App\Http\Requests\Dashboard\RestPasswordRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ForgotAdminsPasswordController extends Controller
{
    public function getEmail()
    {
        return view('dashboard.auth.forgetpassword');
    }

    public function postEmail(ForgotpasswordRequest $request)
    {
        // 1. جلب المستخدم
        $user = User::where('email', $request->email)->first();

        // ✅ 2. التحقق من وجود المستخدم لمنع Fatal Error (لو الإيميل غير موجود)
        if (!$user) {
            notify()->error(__('dashboard.Email_Not_Found'), __('dashboard.error'));
            return back();
        }

        // ✅ 3. التحقق من الصلاحيات عبر Spatie بدلاً من role_id
        // (تأكد من أن أسماء الأدوار هنا تطابق ما لديك في قاعدة البيانات: admin, super_admin)
        if (!$user->hasAnyRole(['admin', 'super_admin'])) {
            notify()->warning(__('dashboard.You_do_not_have_permission_to_access_the_control_panel'), __('dashboard.warning'));
            return back();
        }

        $token = Str::random(64);

        // ✅ 4. استخدام اسم الجدول الصحيح حسب ملف الميجريشن الافتراضي في لارافيل
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token, 'created_at' => Carbon::now()]
        );

        Mail::send('dashboard.auth.sendtokenpassword', ['token' => $token], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject(__('passwords.resetpage'));
        });

        notify()->success(__('passwords.sent'), __('dashboard.success'));
        return back();
    }

    public function getPassword($token)
    {
        return view('dashboard.auth.resetpassword', ['token' => $token]);
    }

    public function updatePassword(RestPasswordRequest $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
        ]);

        // ✅ 5. استخدام اسم الجدول الصحيح
        $updatePassword = DB::table('password_reset_tokens')
            ->where(['email' => $request->email, 'token' => $request->token])
            ->first();

        if (!$updatePassword) {
            notify()->error(__('passwords.token'), __('dashboard.error'));
            return back();
        }

        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        // ✅ 6. حذف التوكن من الجدول الصحيح
        DB::table('password_reset_tokens')->where(['email' => $request->email])->delete();

        notify()->success(__('passwords.change'), __('dashboard.success'));

        return redirect()->route('admin.login');
    }
}
