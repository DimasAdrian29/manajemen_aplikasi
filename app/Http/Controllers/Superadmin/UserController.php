<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Superadmin\UserRequest;
use App\Models\Instansi;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Tampilkan daftar seluruh pengguna sistem.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $roleFilter = $request->query('role');
        $instansiFilter = $request->query('instansi_id');

        $users = User::with(['roles', 'instansi'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($roleFilter, function ($query, $role) {
                $query->role($role);
            })
            ->when($instansiFilter, function ($query, $instansiId) {
                $query->where('instansi_id', $instansiId);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $roles = Role::all();
        $instansis = Instansi::orderBy('nama_instansi')->get();

        return view('superadmin.user.index', compact('users', 'roles', 'instansis', 'search', 'roleFilter', 'instansiFilter'));
    }

    /**
     * Form tambah pengguna baru.
     */
    public function create(): View
    {
        $roles = Role::all();
        $instansis = Instansi::orderBy('nama_instansi')->get();

        return view('superadmin.user.create', compact('roles', 'instansis'));
    }

    /**
     * Simpan pengguna baru dan assign role Spatie.
     */
    public function store(UserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $roleName = $data['role'];

        // Jika bukan role OPD, kosongkan instansi_id
        if ($roleName !== 'opd') {
            $data['instansi_id'] = null;
        }

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);
        $user->assignRole($roleName);

        return redirect()->route('superadmin.user.index')
            ->with('success', "Pengguna {$user->name} berhasil ditambahkan dengan peran {$roleName}.");
    }

    /**
     * Form edit data pengguna.
     */
    public function edit(User $user): View
    {
        $roles = Role::all();
        $instansis = Instansi::orderBy('nama_instansi')->get();

        return view('superadmin.user.edit', compact('user', 'roles', 'instansis'));
    }

    /**
     * Perbarui data pengguna dan sinkronisasi role.
     */
    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        $roleName = $data['role'];

        // Update password jika diisi
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // Jika bukan role OPD, kosongkan instansi_id
        if ($roleName !== 'opd') {
            $data['instansi_id'] = null;
        }

        $user->update($data);
        $user->syncRoles([$roleName]);

        return redirect()->route('superadmin.user.index')
            ->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    /**
     * Hapus akun pengguna.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('superadmin.user.index')
            ->with('success', 'Pengguna berhasil dihapus dari sistem.');
    }
}
