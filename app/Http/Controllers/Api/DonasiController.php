<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DonasiController extends Controller
{
    //

    public function index(){
        $donasi = Donasi::all();
        return response()->json($donasi);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:20',
            'tgl_kirim' => 'required|date',
            'jumlah' => 'required|integer|min:1000',
            'bukti_tf' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $filePath = null;
        if ($request->hasFile('bukti_tf')) {
            $file = $request->file('bukti_tf');
            $fileName = time() . '_' . $file->getClientOriginalName();
            
            $filePath = $file->storeAs('donasi', $fileName, 'public');
        }

        $donation = Donasi::create([
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'no_hp' => $request->no_hp,
            'tgl_kirim' => $request->tgl_kirim,
            'jumlah' => $request->jumlah,
            'bukti_tf' => $filePath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Konfirmasi donasi berhasil dikirim.',
            'data' => $donation
        ], 201);
    }

    public function show($id){
        $donasi = Donasi::find($id);
        return response()->json($donasi);
    }
}
