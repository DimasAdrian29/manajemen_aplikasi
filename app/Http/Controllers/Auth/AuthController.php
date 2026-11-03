<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\OpdRegisterRequest;
use App\Models\Instansi;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
     * Tampilkan formulir pendaftaran akun khusus perwakilan OPD.
     */
    public function showRegisterForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        $instansis = Instansi::orderBy('nama_instansi')->get();

        return view('auth.register', compact('instansis'));
    }

    /**
     * Proses pendaftaran akun mandiri untuk OPD.
     */
    public function register(OpdRegisterRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);
        $user->assignRole('opd');

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('opd.dashboard')
            ->with('success', 'Pendaftaran akun OPD berhasil! Selamat datang di Portal Layanan SIMBANGDA, '.$user->name.'.');
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
