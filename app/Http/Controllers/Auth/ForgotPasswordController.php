<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    /**
     * Ensure the password_resets table exists
     */
    private function ensurePasswordResetsTableExists(): void
    {
        if (!Schema::hasTable('password_resets')) {
            Schema::create('password_resets', function ($table) {
                $table->string('email')->index();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
            Log::info('Created password_resets table on-the-fly');
        }
    }

    /**
     * Show the forgot password form
     */
    public function showForgotPasswordForm()
    {
        try {
            return view('auth.forgot-password');
        } catch (\Exception $e) {
            Log::error('Failed to show forgot password form: ' . $e->getMessage());
            return redirect()->route('login')
                ->withErrors(['email' => 'Password reset is temporarily unavailable. Please try again later.']);
        }
    }

    /**
     * Handle forgot password request - sends reset link via email
     */
    public function sendResetLink(Request $request)
    {
        // Increase execution time for SMTP operations
        set_time_limit(120);

        try {
            $request->validate([
                'email' => 'required|email',
            ]);

            // Check if user exists
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                // Don't reveal if email exists or not (security best practice)
                return back()->with('success', 'If that email exists in our system, we have sent a password reset link.');
            }

            // Ensure the password_resets table exists
            $this->ensurePasswordResetsTableExists();

            // Generate a unique token
            $token = Str::random(64);

            // Delete any existing tokens for this email first, then insert new one
            DB::table('password_resets')->where('email', $request->email)->delete();
            DB::table('password_resets')->insert([
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
            ]);

            // Build the reset link
            $resetLink = url('/reset-password/' . $token . '?email=' . urlencode($request->email));

            // Log mail config for debugging
            Log::info('Sending password reset email', [
                'to' => $request->email,
                'mailer' => config('mail.default'),
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'from' => config('mail.from.address'),
            ]);

            // Send the email
            Mail::send('emails.password-reset', ['link' => $resetLink], function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Password Reset Request - Nish Auto Limited');
            });

            Log::info('Password reset email sent successfully to: ' . $request->email);

            return back()->with('success', 'We have emailed your password reset link! Please check your inbox and spam folder.');

        } catch (\Exception $e) {
            Log::error('Password reset error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withErrors([
                'email' => config('app.debug')
                    ? 'Error: ' . $e->getMessage()
                    : 'Unable to send password reset email. Please try again later or contact support.',
            ]);
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
            'token' => 'required',
        ]);

        try {
            // Ensure table exists
            $this->ensurePasswordResetsTableExists();

            // Check if token exists and is not expired
            $passwordReset = DB::table('password_resets')
                ->where('email', $request->email)
                ->first();

            if (!$passwordReset) {
                return back()->withErrors(['email' => 'Invalid or expired reset token. Please request a new link.']);
            }

            // Check if token matches
            if (!Hash::check($request->token, $passwordReset->token)) {
                return back()->withErrors(['email' => 'Invalid reset token. Please request a new link.']);
            }

            // Check if token is expired (60 minutes)
            $createdAt = Carbon::parse($passwordReset->created_at);
            if ($createdAt->addMinutes(60)->isPast()) {
                // Clean up the expired token
                DB::table('password_resets')->where('email', $request->email)->delete();
                return back()->withErrors(['email' => 'Reset link has expired. Please request a new one.']);
            }

            // Update user password
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return back()->withErrors(['email' => 'User not found.']);
            }

            $user->password = Hash::make($request->password);
            $user->save();

            // Delete the used reset token
            DB::table('password_resets')->where('email', $request->email)->delete();

            Log::info('Password reset successful for: ' . $request->email);

            return redirect()->route('login')->with('success', 'Your password has been reset successfully! Please login with your new password.');

        } catch (\Exception $e) {
            Log::error('Password reset failed', [
                'error' => $e->getMessage(),
                'email' => $request->email,
            ]);

            return back()->withErrors([
                'email' => 'Failed to reset password. Please try again.',
            ]);
        }
    }
}
