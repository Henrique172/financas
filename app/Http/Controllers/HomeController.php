<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Entrie;
use App\Models\Outgoing;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function index() {

        $boletoAtrasados = Outgoing::where('vencimento', '<', date('Y-m-d H:i:s'))->get();
        $receita = Entrie::all();
        $despesaPaga = Outgoing::where('paga', 1)->get();
        $boletoAtrasadosCount = count($boletoAtrasados);
        // dd($despesaPaga);

        // return view('home');
        return view('home',
        [
        'boletoAtrasadosCount' => $boletoAtrasadosCount ,
        'despesaPaga' => $despesaPaga ,
        'receita' => $receita ,
    ]);
    }
}
