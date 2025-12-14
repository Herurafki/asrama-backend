<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Orangtua;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class OrangTuaController extends Controller
{
    //
    public function show(Request $Request )
    {
        $profile = Orangtua::where('user_id', $Request->user()->id)->first();
        return response()->json(['data'=>$profile]);
    }

    public function upsert(Request $request)
    {
        $validated = $request->validate([
            'orangtua.nama_ayah' => 'nullable|string',
            'orangtua.pendidikan_ayah' => 'nullable|string',
            'orangtua.pekerjaan_ayah' => 'nullable|string',
            'orangtua.nama_ibu' => 'nullable|string',
            'orangtua.pendidikan_ibu' => 'nullable|string',
            'orangtua.pekerjaan_ibu' => 'nullable|string',
            'orangtua.nama_wali' => 'nullable|string',
            'orangtua.pekerjaan_wali' => 'nullable|string',
            'orangtua.alamat_wali' => 'nullable|string',
            'orangtua.alamat' => 'nullable|string',
            'orangtua.no_hp' => 'nullable|string',
        ]);

        $payload = Arr::get($validated, 'orangtua', []);

        Orangtua::updateOrCreate(
            ['user_id' => $request->user()->id],
            $payload
        );

        return response()->json(['message' => 'saved']);
    }
}
