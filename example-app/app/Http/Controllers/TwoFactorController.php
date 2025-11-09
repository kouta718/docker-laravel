<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{

    // 以下のshowメソッド・verifyメソッドを追加

    public function show()
    {
        return view('auth.verify-pin');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'two_factor_code' => 'required|digits:6',
        ]);

        $user = Auth::user();

        // メール認証が完了しているか確認
        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice')
                ->withErrors(['email' => 'メール認証が完了していません。']);
        }

        if ($user->two_factor_code !== $request->two_factor_code) {
            return back()->withErrors(['two_factor_code' => 'コードが間違っています。']);
        }
        if (now()->gt($user->two_factor_expires_at)) {
            return back()->withErrors(['two_factor_code' => 'コードの有効期限が切れています。']);
        }

        // 2FAコードをクリア
        $user->two_factor_code = null;
        $user->two_factor_expires_at = null;
        $user->save();

        // 2FA認証状態をセッションに保存
        session(['two_factor_authenticated' => true]);

        // メール認証と2FAが完了した状態でダッシュボードへリダイレクト
        return redirect()->route('dashboard');
    }

    public function regenerate()
    {
        $user = Auth::user();
        $user->two_factor_code = null;
        $user->two_factor_expires_at = null;
        $user->save();
        $user->generateTwoFactorCode();
        return redirect()->route('verify.pin');
    }
}
