<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function portal(): RedirectResponse
    {
        return redirect()->route($this->homeRouteForRole(Auth::user()?->role));
    }

    public function showLogin(Request $request): View
    {
        return view('auth.login', [
            'title' => 'Login Dosen',
            'loginRoute' => route('dosen.login.store'),
            'registerRoute' => route('dosen.register'),
            'defaultEmail' => 'dosen@bimbingan.test',
            'demoText' => 'Akun demo dosen: dosen@bimbingan.test dengan password: password',
            'showRegisterLink' => true,
            'next' => $this->nextRoute($request, ['pa.dosen.dashboard']),
        ]);
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function showStudentLogin(Request $request): View
    {
        return view('auth.login', [
            'title' => 'Login Mahasiswa',
            'loginRoute' => route('mahasiswa.login.store'),
            'registerRoute' => route('mahasiswa.register'),
            'defaultEmail' => 'mahasiswa@bimbingan.test',
            'demoText' => 'Akun demo mahasiswa: mahasiswa@bimbingan.test dengan password: password',
            'showRegisterLink' => true,
            'next' => $this->nextRoute($request, ['pa.mahasiswa.dashboard']),
        ]);
    }

    public function showStudentRegister(): View
    {
        return view('auth.student-register');
    }

    public function showAdminLogin(): View
    {
        return view('auth.login', [
            'title' => 'Login Admin',
            'loginRoute' => route('admin.login.store'),
            'registerRoute' => null,
            'defaultEmail' => 'admin@bimbingan.test',
            'demoText' => 'Akun demo admin: admin@bimbingan.test dengan password: password',
            'showRegisterLink' => false,
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            if (! in_array(Auth::user()->role, ['dosen', 'examiner'], true)) {
                Auth::logout();

                return back()
                    ->withErrors(['email' => 'Akun ini bukan akun dosen atau penguji.'])
                    ->onlyInput('email');
            }

            $request->session()->regenerate();

            return redirect()->intended(route($this->homeRouteForRole(Auth::user()->role)));
        }

        return back()
            ->withErrors(['email' => 'Email atau password tidak sesuai.'])
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing')->with('status', 'Anda berhasil logout.');
    }

    public function showPasswordForm(): View
    {
        return view('auth.change-password', [
            'title' => 'Ubah Password',
            'roleLabel' => match (Auth::user()?->role) {
                'admin' => 'Admin',
                'mahasiswa' => 'Mahasiswa',
                'dosen' => 'Dosen',
                default => 'User',
            },
        ]);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        if (! Hash::check($validated['current_password'], $request->user()->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }

        $request->user()->forceFill([
            'password' => Hash::make($validated['password']),
        ])->save();

        $request->session()->regenerate();

        return back()->with('status', 'Password berhasil diperbarui.');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email', 'unique:lecturers,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'nip' => ['required', 'max:255', 'unique:lecturers,nip'],
            'nidn' => ['nullable', 'max:255'],
            'employment_status' => ['required', 'max:255'],
            'expertise' => ['required', 'max:255'],
            'gender' => ['nullable', 'max:50'],
            'birth_place' => ['nullable', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'phone' => ['nullable', 'max:50'],
            'address' => ['nullable', 'max:500'],
        ]);

        $user = DB::transaction(function () use ($validated) {
            $userId = DB::table('users')->insertGetId([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => 'dosen',
                'password' => Hash::make($validated['password']),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('lecturers')->insert([
                'user_id' => $userId,
                'nip' => $validated['nip'],
                'nidn' => $validated['nidn'] ?? null,
                'certificate_number' => null,
                'employment_status' => $validated['employment_status'],
                'expertise' => $validated['expertise'],
                'name' => $validated['name'],
                'gender' => $validated['gender'] ?? null,
                'birth_place' => $validated['birth_place'] ?? null,
                'birth_date' => $validated['birth_date'] ?? null,
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return \App\Models\User::findOrFail($userId);
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route($this->homeRouteForRole('dosen'))->with('status', 'Registrasi berhasil. Selamat datang.');
    }

    public function studentLogin(Request $request)
    {
        return $this->loginForRole($request, 'mahasiswa', route($this->homeRouteForRole('mahasiswa')), 'Akun ini bukan akun mahasiswa.');
    }

    public function adminLogin(Request $request)
    {
        return $this->loginForRole($request, 'admin', route($this->homeRouteForRole('admin')), 'Akun ini bukan akun admin.');
    }

    public function studentRegister(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email', 'unique:students,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'nim' => ['required', 'max:255', 'unique:students,nim'],
            'program' => ['required', 'max:255'],
        ]);

        $user = DB::transaction(function () use ($validated) {
            $userId = DB::table('users')->insertGetId([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => 'mahasiswa',
                'password' => Hash::make($validated['password']),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('students')->insert([
                'user_id' => $userId,
                'nim' => $validated['nim'],
                'name' => $validated['name'],
                'program' => $validated['program'],
                'email' => $validated['email'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return \App\Models\User::findOrFail($userId);
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route($this->homeRouteForRole('mahasiswa'))->with('status', 'Registrasi mahasiswa berhasil.');
    }

    private function loginForRole(Request $request, string $role, string $redirectTo, string $roleError, array $allowedNextRoutes = [])
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email atau password tidak sesuai.'])
                ->onlyInput('email');
        }

        if (Auth::user()->role !== $role) {
            Auth::logout();

            return back()
                ->withErrors(['email' => $roleError])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        if ($request->filled('next') && in_array($request->input('next'), $allowedNextRoutes, true)) {
            return redirect()->route($request->input('next'));
        }

        return redirect()->intended($redirectTo);
    }

    private function nextRoute(Request $request, array $allowedRoutes): ?string
    {
        $next = $request->query('next');

        if (is_string($next) && in_array($next, $allowedRoutes, true)) {
            return $next;
        }

        return null;
    }

    private function homeRouteForRole(?string $role): string
    {
        return match ($role) {
            'admin' => 'admin.profile',
            'mahasiswa' => 'mahasiswa.profile',
            'examiner' => 'profile',
            default => 'profile',
        };
    }
}
