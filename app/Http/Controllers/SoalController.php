<?php

namespace App\Http\Controllers;

use App\Models\Soal;
use App\Http\Requests\StoreSoalRequest;
use App\Http\Requests\UpdateSoalRequest;

class SoalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $soal = Soal::with('materi.kelas')->get();
        return response()->json([
            'success' => true,
            'message' => 'Daftar soal berhasil diambil',
            'data' => $soal
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
    public function store(StoreSoalRequest $request)
    {
        $soal = Soal::create($request->validated());
        $soal->load('materi.kelas');

        return response()->json([
            'success' => true,
            'message' => 'Soal berhasil dibuat',
            'data' => $soal
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Soal $soal)
    {
        $soal->load('materi.kelas');
        return response()->json([
            'success' => true,
            'message' => 'Detail soal berhasil diambil',
            'data' => $soal
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Soal $soal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSoalRequest $request, Soal $soal)
    {
        $soal->update($request->validated());
        $soal->load('materi.kelas');

        return response()->json([
            'success' => true,
            'message' => 'Soal berhasil diupdate',
            'data' => $soal
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Soal $soal)
    {
        $soal->delete();

        return response()->json([
            'success' => true,
            'message' => 'Soal berhasil dihapus'
        ]);
    }

    public function getByMateri($materi_id)
    {
        $soal = Soal::where('materi_id', $materi_id)->get();
        return response()->json([
            'success' => true,
            'message' => 'Soal per materi berhasil diambil',
            'data' => $soal
        ]);
    }
}
