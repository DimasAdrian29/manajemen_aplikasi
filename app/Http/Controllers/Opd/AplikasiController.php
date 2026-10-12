<?php

namespace App\Http\Controllers\Opd;

use App\Http\Controllers\Controller;
use App\Models\Aplikasi;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AplikasiController extends Controller
{
    /**
     * Tampilkan katalog aplikasi milik OPD.
     */
    public function index(Request $request): View
    {
        $instansiId = auth()->user()->instansi_id;
        $search = $request->query('search');
        $status = $request->query('status');

        $aplikasis = Aplikasi::where('instansi_id', $instansiId)
            ->when($search, function ($query, $search) {
                $query->where('nama_aplikasi', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('opd.aplikasi.index', compact('aplikasis', 'search', 'status'));
    }
}
