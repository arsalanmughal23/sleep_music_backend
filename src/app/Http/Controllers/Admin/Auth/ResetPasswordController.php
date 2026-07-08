<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Helper\Util;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ResetPasswordForWebRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */

    // H (hours) * M (minutes) * S (seconds) * MS (miliseconds)
    public const OTP_EXPIRE_IN_MILISECONDS = 1 * 60 * 60 * 1000;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        $check = DB::table('password_resets')->where(['code' => $token])->first();
        
        if(!$check){
            return redirect()->route('admin.password.request')->with('error', 'Invalid token.');
        }
        
        if(Util::checkExpiry($check->created_at, self::OTP_EXPIRE_IN_MILISECONDS)){
            DB::table('password_resets')->where(['code' => $token])->delete();
            return redirect()->route('admin.password.request')->with('error', 'Token is expire.');
        }

        return view('admin.auth.passwords.reset')->with(
            ['token' => $token, 'email' => $check->email]
        );
    }
    
    public function resetPassword(ResetPasswordForWebRequest $request)
    {
        $token = $request->input('token');
        $email = $request->input('email');
        $password = $request->input('password');

        $reset = DB::table('password_resets')
            ->where('email', $email)
            ->where('code', $token)
            ->first();

        if (!$reset) {
            return redirect()->route('admin.password.request')->with('error', 'Invalid token.');
        }
        
        if(Util::checkExpiry($reset->created_at, self::OTP_EXPIRE_IN_MILISECONDS)){
            DB::table('password_resets')->where(['email' => $email, 'code' => $token])->delete();
            return redirect()->route('admin.password.request')->with('error', 'Token is expire.');
        }

        // Update the user's password
        User::where('email', $email)
            ->update(['password' => Hash::make($password)]);

        // Delete the reset token from the table
        DB::table('password_resets')
            ->where('email', $email)
            ->delete();

        return redirect()->route('admin.login')->with('message', 'Password reset successfully.');
    }
}
