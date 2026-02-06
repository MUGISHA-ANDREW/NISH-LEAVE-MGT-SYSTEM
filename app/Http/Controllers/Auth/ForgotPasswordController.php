<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    /**
     * Show the forgot password form
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle forgot password request
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        try {
            // Generate a unique token
            $token = Str::random(64);

            // Store the token in the password_resets table
            DB::table('password_resets')->updateOrInsert(
                ['email' => $request->email],
                [
                    'token' => Hash::make($token),
                    'created_at' => Carbon::now()
                ]
            );

            // Send the reset link via email
            $resetLink = url('/reset-password/' . $token . '?email=' . urlencode($request->email));
            
            // Send email with proper error handling
            try {
                Mail::send('emails.password-reset', ['link' => $resetLink], function($message) use ($request) {
                    $message->to($request->email);
                    $message->subject('Password Reset Request - Nish Auto Limited');
                });
                
                // Check if email was queued or sent successfully
                if (count(Mail::failures()) > 0) {
                    throw new \Exception('Failed to send email to: ' . $request->email);
                }
                
                Log::info('Password reset email sent successfully to: ' . $request->email);
                $successMessage = 'We have emailed your password reset link! Please check your inbox and spam folder.';
            } catch (\Exception $emailError) {
                // Email sending failed, log for admin but don't expose details to user
                Log::error('Password reset email failed for: ' . $request->email);
                Log::error('Error: ' . $emailError->getMessage());
                Log::info('Reset link (for admin): ' . $resetLink);
                
                $successMessage = 'We have sent a password reset request. If you don\'t receive an email within a few minutes, please contact your administrator.';
            }

            return back()->with('success', $successMessage);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Password reset error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return back()->withErrors(['email' => 'Failed to process password reset. Please try again later.']);
        }
    }

    /**
     * Show the reset password form
     */
    public function showResetPasswordForm($token, Request $request)
    {
        $email = $request->query('email');
        
        if (!$email) {
            return redirect()->route('login')->withErrors(['email' => 'Invalid reset link.']);
        }

        return view('auth.reset-password', ['token' => $token, 'email' => $email]);
    }

    /**
     * Handle password reset
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
            'token' => 'required'
        ]);

        // Check if token exists and is not expired (valid for 60 minutes)
        $passwordReset = DB::table('password_resets')
            ->where('email', $request->email)
            ->first();

        if (!$passwordReset) {
            return back()->withErrors(['email' => 'Invalid reset token.']);
        }

        // Check if token matches
        if (!Hash::check($request->token, $passwordReset->token)) {
            return back()->withErrors(['email' => 'Invalid reset token.']);
        }

        // Check if token is expired (60 minutes)
        $createdAt = Carbon::parse($passwordReset->created_at);
        if ($createdAt->addMinutes(60)->isPast()) {
            return back()->withErrors(['email' => 'Reset link has expired. Please request a new one.']);
        }

        // Update user password
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the reset token
        DB::table('password_resets')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Your password has been reset successfully!');
    }
}
