<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

/**
 * تسجيل دخول وتسجيل زبائن المتجر.
 * لوحة الإدارة (Filament) للموظفين فقط ولها صفحة دخول مستقلة.
 */
class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.']);
        }

        $request->session()->regenerate();

        // الموظفون إلى لوحة الإدارة، والزبائن إلى المتجر
        if (Auth::user()->role !== 'customer') {
            return redirect()->intended('/admin');
        }

        return redirect()->intended('/');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // الدور يُحدد هنا صراحةً ولا يأتي أبداً من الطلب
        $user = new User([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password'],
        ]);
        $user->role = 'customer';
        $user->save();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended('/')->with('success', 'أهلاً بك! تم إنشاء حسابك بنجاح.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
