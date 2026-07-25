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
        $user = User::where('email', $request->email)->first();
            if (in_array($user->role_id, [1, 2, 3])) {
            $token = Str::random(64);

            DB::table('password_resets')->insert(
                ['email' => $request->email, 'token' => $token, 'created_at' => Carbon::now()]
            );

            Mail::send('dashboard.auth.sendtokenpassword', ['token' => $token], function ($message) use ($request) {
                $message->to($request->email);
                $message->subject(__('passwords.resetpage'));
            });
            notify()->success(__('passwords.sent'), __('dashboard.success'));
            return back();
        } else {
            notify()->warning(__('dashboard.You_do_not_have_permission_to_access_the_control_panel'), __('dashboard.warning'));
            return back();
        }
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

        $updatePassword = DB::table('password_resets')
            ->where(['email' => $request->email, 'token' => $request->token])
            ->first();

        if (!$updatePassword) {
            notify()->error(__('passwords.token'), __('dashboard.error'));
            return back();
        }


        $user = User::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        DB::table('password_resets')->where(['email' => $request->email])->delete();
        notify()->success(__('passwords.change'), __('dashboard.success'));

        return redirect()->route('admin.login');
    }
}
