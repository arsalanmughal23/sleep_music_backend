<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Constants\EmailServiceTemplateNames;
use App\Http\Controllers\Controller;
use App\Jobs\SendEmail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */


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
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLinkRequestForm()
    {
        return view('admin.auth.passwords.email');
    }
    
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email,deleted_at,NULL'
        ]);
        
        try{
            $email = $request->input('email');
            $user = User::whereEmail($email)->first();
            $code = Str::random(64);
            // $code = rand(1111, 9999);            
            
            if(!$user){
                return redirect()->back()->with('error', 'Email is not exists.');
            }

            if(!$user->hasRole(['super-admin', 'admin'])){
                return redirect()->back()->with('error', 'You are not eligible to perform this action.');
            }

            $reset_link = route('admin.password.reset', $code);
            $data = [
                'first_name' => $user->name ?? $user->details->first_name,
                'reset_password_link' => $reset_link
            ];

            $check = DB::table('password_resets')->where('user_id', $user->id)->first();
            if ($check) {
                DB::table('password_resets')->where('user_id', $user->id)->delete();
            }
            DB::table('password_resets')->insert(['user_id' => $user->id, 'email' => $user->email, 'code' => $code, 'created_at' => Carbon::now()]);
            
            // Send email with reset link
            $subject = 'Forgot Password Verification Code';
            $sendEmailJob = new SendEmail($email, $subject, $data, EmailServiceTemplateNames::RESET_PASSWORD_LINK_TEMPLATE);
            dispatch($sendEmailJob);

            return back()->with('message', 'Reset link sent to your email.');

        } catch (\Exception $e) {
            return $this->sendErrorWithData([$e->getMessage()], 403);
        }
    }
}
