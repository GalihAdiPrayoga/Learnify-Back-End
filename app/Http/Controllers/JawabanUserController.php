<?php

namespace App\Http\Controllers;

use App\Models\JawabanUser;
use App\Models\Soal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JawabanUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(Request $request)
    {
        $request->validate([
            'hasil_ujian_id' => 'required|exists:hasil_ujians,id',
            'soal_id' => 'required|exists:soals,id',
            'jawaban_user' => 'required|string',
        ]);

        $soal = Soal::find($request->soal_id);
        $benar = ($request->jawaban_user === $soal->jawaban_benar);

        $jawaban = JawabanUser::updateOrCreate(
            [
                'hasil_ujian_id' => $request->hasil_ujian_id,
                'soal_id' => $request->soal_id,
                'user_id' => Auth::id(),
            ],
            [
                'jawaban_user' => $request->jawaban_user,
                'jawaban_benar' => $benar,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Jawaban disimpan',
            'data' => $jawaban
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
