<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use Illuminate\Http\Request;

class KamarController extends Controller
{
    //
    public function index(){
        $kamar = Kamar::all();
        return response()->json($kamar);
    }

    public function show($id){
        $kamar = Kamar::find($id);
        return response()->json($kamar);
    }
}
