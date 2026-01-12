<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHasilUjianRequest;
use App\Http\Requests\UpdateHasilUjianRequest;
use App\Models\HasilUjian;
use App\Models\Soal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HasilUjianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $hasil = HasilUjian::with(['user', 'materi'])->get();
        return response()->json([
            'success' => true,
            'data' => $hasil
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreHasilUjianRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $hasil = HasilUjian::with(['materi.kelas', 'jawabanUsers.soal'])->find($id);

        if (!$hasil || $hasil->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $hasil
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HasilUjian $hasilUjian)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateHasilUjianRequest $request, HasilUjian $hasilUjian)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HasilUjian $hasilUjian)
    {
        $hasilUjian->delete();
        return response()->json(['success' => true, 'message' => 'Hasil ujian dihapus']);
    }

    /**
     * POST /user/hasil-ujian/start
     * Mulai ujian: buat record HasilUjian dengan nilai 0
     */
    public function start(Request $request)
    {
        $request->validate(['materi_id' => 'required|exists:materis,id']);

        // ✅ Guard: cek apakah sudah ada session aktif (dalam 1 jam terakhir)
        $existing = HasilUjian::where('user_id', Auth::id())
            ->where('materi_id', $request->materi_id)
            ->where('created_at', '>=', now()->subHour())
            ->where('nilai', 0) // session yang belum finish
            ->first();

        if ($existing) {
            return response()->json([
                'success' => true,
                'message' => 'Melanjutkan sesi ujian yang ada',
                'data' => $existing
            ]);
        }

        $soalCount = Soal::where('materi_id', $request->materi_id)->count();

        $hasil = HasilUjian::create([
            'user_id' => Auth::id(),
            'materi_id' => $request->materi_id,
            'jumlah_soal' => $soalCount,
            'jumlah_benar' => 0,
            'nilai' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ujian dimulai',
            'data' => $hasil
        ], 201);
    }

    /**
     * POST /user/hasil-ujian/finish
     * Hitung nilai berdasarkan jawaban yang sudah disimpan
     */
    public function finish(Request $request)
    {
        $request->validate(['hasil_ujian_id' => 'required|exists:hasil_ujians,id']);

        $hasil = HasilUjian::with('jawabanUsers.soal')->find($request->hasil_ujian_id);

        if (!$hasil || $hasil->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $forceFail = (bool) $request->input('force_fail', false);

        // If already finished, return existing (keep previous guard)
        if ($hasil->nilai > 0 || $hasil->jumlah_benar > 0 || $hasil->updated_at->diffInSeconds($hasil->created_at) > 5) {
            return response()->json([
                'success' => true,
                'message' => 'Ujian sudah selesai sebelumnya',
                'data' => $hasil
            ]);
        }

        // If time expired and caller requested force-fail, mark as failed (0 score)
        if ($forceFail) {
            $hasil->update([
                'jumlah_benar' => 0,
                'nilai' => 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Waktu habis. Ujian dinyatakan gagal.',
                'data' => $hasil->fresh(['jawabanUsers.soal'])
            ]);
        }

        $jumlahBenar = $hasil->jawabanUsers->where('jawaban_benar', true)->count();
        $jumlahSoal = $hasil->jumlah_soal;
        $nilai = $jumlahSoal > 0 ? round(($jumlahBenar / $jumlahSoal) * 100) : 0;

        $hasil->update([
            'jumlah_benar' => $jumlahBenar,
            'nilai' => $nilai,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ujian selesai',
            'data' => $hasil->fresh(['jawabanUsers.soal'])
        ]);
    }

    /**
     * GET /user/hasil-ujian (history for current user)
     */
    public function userHistory()
    {
        $history = HasilUjian::with(['materi.kelas'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }
}
