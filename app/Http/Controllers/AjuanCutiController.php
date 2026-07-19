<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AjuanCuti;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class AjuanCutiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = AjuanCuti::orderBy('created_at', 'desc');

        // Jika pegawai, hanya tampilkan cuti miliknya sendiri
        if ($user->role === 'pegawai') {
            $query->where('nip', $user->nip);
        }

        // Optional filter by status
        if ($request->has('status') && in_array($request->status, ['Menunggu', 'Disetujui', 'Ditolak'])) {
            $query->where('status', $request->status);
        }

        // Optional search by NIP or Name (hanya berguna untuk admin)
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nip', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        $ajuanCutis = $query->paginate(10)->withQueryString();

        // Hitung statistik (pegawai hanya melihat statistiknya sendiri, admin melihat semua)
        if ($user->role === 'pegawai') {
            $stats = [
                'total' => AjuanCuti::where('nip', $user->nip)->count(),
                'menunggu' => AjuanCuti::where('nip', $user->nip)->where('status', 'Menunggu')->count(),
                'disetujui' => AjuanCuti::where('nip', $user->nip)->where('status', 'Disetujui')->count(),
                'ditolak' => AjuanCuti::where('nip', $user->nip)->where('status', 'Ditolak')->count(),
            ];
        } else {
            $stats = [
                'total' => AjuanCuti::count(),
                'menunggu' => AjuanCuti::where('status', 'Menunggu')->count(),
                'disetujui' => AjuanCuti::where('status', 'Disetujui')->count(),
                'ditolak' => AjuanCuti::where('status', 'Ditolak')->count(),
            ];
        }

        return view('dashboard', compact('ajuanCutis', 'stats'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Pegawai tidak boleh mengakses fungsi ini jika role-nya bukan pegawai
        if ($user->role !== 'pegawai') {
            return redirect()->route('dashboard')->with('error', 'Hanya pegawai yang dapat mengajukan cuti.');
        }

        $validated = $request->validate([
            'jenis_cuti' => [
                'required',
                Rule::in(['Cuti Tahunan', 'Cuti Sakit', 'Cuti Alasan Penting', 'Cuti Melahirkan']),
            ],
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'nullable|string',
        ], [
            'jenis_cuti.required' => 'Jenis cuti wajib dipilih.',
            'jenis_cuti.in' => 'Jenis cuti tidak valid.',
            'tanggal_mulai.required' => 'Tanggal mulai cuti wajib diisi.',
            'tanggal_mulai.date' => 'Format tanggal mulai tidak valid.',
            'tanggal_selesai.required' => 'Tanggal selesai cuti wajib diisi.',
            'tanggal_selesai.date' => 'Format tanggal selesai tidak valid.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
        ]);

        // Otomatis isi NIP dan Nama dari user yang login
        $validated['nip'] = $user->nip;
        $validated['nama'] = $user->name;
        $validated['status'] = 'Menunggu';

        AjuanCuti::create($validated);

        return redirect()->route('dashboard')->with('success', 'Ajuan cuti berhasil dikirim.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $ajuan = AjuanCuti::findOrFail($id);

        // Hanya pemilik ajuan cuti yang bisa mengedit
        if ($ajuan->nip !== $user->nip) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki hak untuk mengedit ajuan ini.');
        }

        if ($ajuan->status !== 'Menunggu') {
            return redirect()->route('dashboard')->with('error', 'Ajuan cuti yang sudah diproses tidak dapat diubah.');
        }

        $validated = $request->validate([
            'jenis_cuti' => [
                'required',
                Rule::in(['Cuti Tahunan', 'Cuti Sakit', 'Cuti Alasan Penting', 'Cuti Melahirkan']),
            ],
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'nullable|string',
        ], [
            'jenis_cuti.required' => 'Jenis cuti wajib dipilih.',
            'jenis_cuti.in' => 'Jenis cuti tidak valid.',
            'tanggal_mulai.required' => 'Tanggal mulai cuti wajib diisi.',
            'tanggal_mulai.date' => 'Format tanggal mulai tidak valid.',
            'tanggal_selesai.required' => 'Tanggal selesai cuti wajib diisi.',
            'tanggal_selesai.date' => 'Format tanggal selesai tidak valid.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
        ]);

        $ajuan->update($validated);

        return redirect()->route('dashboard')->with('success', 'Ajuan cuti berhasil diperbarui.');
    }

    /**
     * Update the status of the leave request (Admin Only).
     */
    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Hanya admin yang dapat memproses status ajuan cuti.');
        }

        $ajuan = AjuanCuti::findOrFail($id);

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in(['Disetujui', 'Ditolak']),
            ],
        ]);

        $ajuan->update([
            'status' => $validated['status']
        ]);

        return redirect()->route('dashboard')->with('success', "Status ajuan cuti atas nama {$ajuan->nama} telah diubah menjadi {$validated['status']}.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $ajuan = AjuanCuti::findOrFail($id);
        $namaPegawai = $ajuan->nama;

        // Pegawai hanya bisa menghapus ajuan miliknya sendiri, Admin bisa menghapus apa saja
        if ($user->role !== 'admin' && $ajuan->nip !== $user->nip) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki hak untuk membatalkan ajuan ini.');
        }

        $ajuan->delete();

        return redirect()->route('dashboard')->with('success', "Ajuan cuti atas nama {$namaPegawai} telah dibatalkan.");
    }
}
