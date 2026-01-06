<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use App\Models\Kelas;
use Illuminate\Http\Request;
use App\Http\Requests\StoreMateriRequest;
use App\Http\Requests\UpdateMateriRequest;

class MateriController extends Controller
{
    /**
     * GET /materi
     */
    public function index(Request $request)
    {
        $query = Materi::withoutGlobalScopes()->with('kelas');

        if ($request->query('kelas_id')) {
            $query->where('kelas_id', $request->query('kelas_id'));
        }

        $materi = $query->orderBy('created_at', 'asc')->get()->map(function ($m) {
            $m->kelas_nama = $m->kelas ? $m->kelas->nama : null;
            return $m;
        });

        return response()->json([
            'success' => true,
            'data' => $materi
        ]);
    }


    /**
     * POST /materi
     */
    public function store(StoreMateriRequest $request)
    {
        $data = $request->validated();
        $materi = Materi::create($data);
        $materi->load('kelas');
        $materi->kelas_nama = $materi->kelas ? $materi->kelas->nama : null;

        return response()->json([
            'success' => true,
            'message' => 'Materi berhasil ditambahkan',
            'data' => $materi
        ], 201);
    }

    /**
     * GET /materi/{materi}
     */
    public function show(Materi $materi)
    {
        $materi->load('kelas');

        return response()->json([
            'success' => true,
            'data' => $materi
        ]);
    }

    /**
     * PUT /materi/{materi}
     */
    public function update(UpdateMateriRequest $request, Materi $materi)
    {
        $data = $request->validated();
        $materi->update($data);
        $materi->load('kelas');
        $materi->kelas_nama = $materi->kelas ? $materi->kelas->nama : null;

        return response()->json([
            'success' => true,
            'message' => 'Materi berhasil diperbarui',
            'data' => $materi
        ]);
    }

    /**
     * DELETE /materi/{materi}
     */
    public function destroy(Materi $materi)
    {
        $materi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Materi berhasil dihapus'
        ]);
    }

    /**
     * GET /user/materi/kelas/{kelas_id}
     * Endpoint untuk user melihat materi berdasarkan kelas
     */
    public function getByKelas($kelas_id)
    {
        // Validasi kelas exists
        $kelas = Kelas::find($kelas_id);

        if (!$kelas) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas tidak ditemukan'
            ], 404);
        }

        // Ambil semua materi dari kelas tersebut
        $materi = Materi::with('kelas')
            ->where('kelas_id', $kelas_id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($m) {
                $m->kelas_nama = $m->kelas ? $m->kelas->nama : null;
                return $m;
            });

        return response()->json([
            'success' => true,
            'data' => $materi
        ]);
    }

    /**
     * GET /user/materi/{id}
     * Endpoint untuk user melihat detail materi
     */
    public function getDetailForUser($id)
    {
        $materi = Materi::with('kelas')->find($id);

        if (!$materi) {
            return response()->json([
                'success' => false,
                'message' => 'Materi tidak ditemukan'
            ], 404);
        }

        $materi->kelas_nama = $materi->kelas ? $materi->kelas->nama : null;

        return response()->json([
            'success' => true,
            'data' => $materi
        ]);
    }
}