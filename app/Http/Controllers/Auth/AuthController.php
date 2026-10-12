<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Proses autentikasi login.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $loginInput = $request->validated('login');
        $password = $request->validated('password');
        $remember = $request->boolean('remember');

        // Tentukan apakah input berupa email atau username
        $fieldType = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $fieldType => $loginInput,
            'password' => $password,
        ];

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();

            return $this->redirectBasedOnRole($user)
                ->with('success', 'Selamat datang kembali, '.$user->name.'!');
        }

        return back()
            ->withInput($request->only('login', 'remember'))
            ->withErrors([
                'login' => 'Kredensial yang dimasukkan tidak sesuai dengan data kami.',
            ]);
    }

    /**
     * Proses logout dari sistem.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil keluar dari sistem.');
    }

    /**
     * Arahkan pengguna ke dashboard sesuai peran Spatie yang dimiliki.
     */
    protected function redirectBasedOnRole(User $user): RedirectResponse
    {
        $targetRoute = $user->dashboardRoute();

        // Cegah infinite redirect jika intended URL mengarah ke root / atau /login
        $intended = session('url.intended');
        if (! $intended || $intended === url('/') || $intended === route('login')) {
            session()->forget('url.intended');

            return redirect()->to($targetRoute);
        }

        return redirect()->intended($targetRoute);
    }
}
