<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $user = $request->user();
        if ($user->hasVerifiedEmail()) {
            // 既に認証済みの場合は2FAコードを発行して2FA画面へ
            $user->generateTwoFactorCode();
            return redirect()->route('verify.pin');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // メール認証完了後、2FAコードを発行して2FA入力画面へ誘導
        $user->generateTwoFactorCode();
        return redirect()->route('verify.pin');
    }
}
