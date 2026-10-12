<?php

namespace App\Http\Controllers\Eksekutif;

use App\Http\Controllers\Controller;
use App\Models\Aplikasi;
use App\Models\Instansi;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AplikasiController extends Controller
{
    /**
     * Tampilkan katalog inventaris aplikasi seluruh OPD se-Provinsi Riau.
     */
    public function index(Request $request): View
    {
        $query = Aplikasi::with(['instansi', 'project']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama_aplikasi', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%")
                    ->orWhereHas('instansi', function ($instQ) use ($search) {
                        $instQ->where('nama_instansi', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('instansi_id')) {
            $query->where('instansi_id', $request->input('instansi_id'));
        }

        $aplikasis = $query->orderBy('nama_aplikasi')->paginate(12)->withQueryString();
        $instansis = Instansi::orderBy('nama_instansi')->get();

        $counts = [
            'total' => Aplikasi::count(),
            'aktif' => Aplikasi::where('status', 'aktif')->count(),
            'dalam_pengembangan' => Aplikasi::where('status', 'dalam_pengembangan')->count(),
            'tidak_aktif' => Aplikasi::where('status', 'tidak_aktif')->count(),
        ];

        return view('eksekutif.aplikasi.index', compact('aplikasis', 'instansis', 'counts'));
    }
}
