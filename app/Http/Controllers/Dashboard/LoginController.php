<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\LoginControllerRequest;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use URL;

class LoginController extends Controller
{

    public function login()
    {
        Session::put('url.intended', url()->previous());

        return view('dashboard.auth.login');
    }

    public function postlogin(LoginControllerRequest $request)
    {
        $type_login = filter_var($request->input('email'), FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';

        $remeber_me = $request->has('remeber_me') ? true : false;
        if (auth()->guard('admin')->attempt([$type_login => $request->input('email'), 'password' => $request->input('password')], $remeber_me)) {
            $user = auth()->guard('admin')->user();
            if ($user->hasRole('admin') || $user->hasRole('employee')) {
                if ($user->is_active == 1) {
                    return redirect()->route('admin.dashboard');
                    //  return Redirect::to(Session::get('url.intended'));
                } else {
                    notify()->warning(__('dashboard.The_account_is_not_activated'), __('dashboard.warning'));
                    auth()->guard('admin')->logout();
                    return redirect()->route('admin.login')->withInput();
                }
            } else {
                notify()->warning(__('dashboard.You_do_not_have_permission_to_access_the_control_panel'), __('dashboard.warning'));
                auth()->guard('admin')->logout();
                return redirect()->route('admin.login')->withInput();
            }
        } else {
            notify()->error(__('dashboard.error_login'), __('dashboard.error'));
            return redirect()->back()->withInput();
        }
    }



    public function logout()
    {
        auth()->guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
