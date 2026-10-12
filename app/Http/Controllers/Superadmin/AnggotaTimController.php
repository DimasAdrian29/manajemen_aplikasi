<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Superadmin\AnggotaTimRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AnggotaTimController extends Controller
{
    /**
     * Tampilkan daftar seluruh anggota tim pengembang (Tenaga Teknis).
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $anggotaTims = User::whereHas('roles', fn ($q) => $q->whereIn('name', ['ketua_tim', 'anggota_tim']))
            ->withCount([
                'timProjects',
                'timProjects as ketua_count' => function ($q) {
                    $q->where('peran', 'ketua_tim');
                },
                'timProjects as anggota_count' => function ($q) {
                    $q->where('peran', 'anggota');
                },
            ])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('no_hp', 'like', "%{$search}%")
                        ->orWhere('jabatan', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('superadmin.anggota-tim.index', compact('anggotaTims', 'search'));
    }

    /**
     * Form tambah personil tim pengembang baru.
     */
    public function create(): View
    {
        return view('superadmin.anggota-tim.create');
    }

    /**
     * Simpan personil tim pengembang baru.
     */
    public function store(AnggotaTimRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['password'] = Hash::make($data['password']);

        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'no_hp' => $data['no_hp'] ?? null,
            'jabatan' => $data['jabatan'] ?? null,
            'password' => $data['password'],
        ]);

        $user->assignRole('anggota_tim');

        return redirect()->route('superadmin.anggota-tim.index')
            ->with('success', "Personil {$user->name} berhasil ditambahkan ke tim pengembang.");
    }

    /**
     * Form edit personil tim pengembang.
     */
    public function edit(User $anggota_tim): View
    {
        return view('superadmin.anggota-tim.edit', compact('anggota_tim'));
    }

    /**
     * Perbarui data personil tim pengembang.
     */
    public function update(AnggotaTimRequest $request, User $anggota_tim): RedirectResponse
    {
        $data = $request->validated();

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $anggota_tim->update($data);
        $roleNames = $anggota_tim->roles->pluck('name')->toArray();
        if (! in_array('anggota_tim', $roleNames, true) && ! in_array('ketua_tim', $roleNames, true)) {
            $anggota_tim->assignRole('anggota_tim');
        }

        return redirect()->route('superadmin.anggota-tim.index')
            ->with('success', "Data personil {$anggota_tim->name} berhasil diperbarui.");
    }

    /**
     * Hapus personil tim pengembang.
     */
    public function destroy(User $anggota_tim): RedirectResponse
    {
        if ($anggota_tim->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($anggota_tim->timProjects()->exists()) {
            return back()->with('error', 'Personil tim tidak dapat dihapus karena masih memiliki riwayat penugasan proyek.');
        }

        $anggota_tim->delete();

        return redirect()->route('superadmin.anggota-tim.index')
            ->with('success', 'Personil tim pengembang berhasil dihapus dari sistem.');
    }
}
