<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;


class MotivoIvaController extends Controller
{

    public function listarMotivos()
    {

        dd(auth()->user());

       

        // if ($empresa->tipo_regime_id == $regimeNaoSujeicao) {
        //     $tipoMotivoIva = DB::connection('mysql2')->table('motivo')
        //         ->whereIn('codigo', [7, 8])->where('empresa_id', NULL)->orWhere('empresa_id', $empresa->id)
        //         ->get();
        // } else if ($empresa->tipo_regime_id == $regimeGeral) {

        //     $tipoMotivoIva = DB::connection('mysql2')->table('motivo')
        //         ->where('empresa_id', NULL)->where('codigo', '!=', 9)
        //         ->where('codigo', '!=', 7)
        //         //->where('codigo','!=',8)
        //         ->orWhere('empresa_id', $empresa->id)
        //         ->get();
        // } else if ($empresa->tipo_regime_id == $regimeTransitorio) {

        //     $tipoMotivoIva = DB::connection('mysql2')->table('motivo')
        //         ->whereIn('codigo', [8, 9])->where('empresa_id', NULL)->orWhere('empresa_id', $empresa->id)
        //         ->get();
        // }
        // return response()->json($tipoMotivoIva, 200);
    }
}
