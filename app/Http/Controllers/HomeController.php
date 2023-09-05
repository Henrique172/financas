<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Entrie;
use App\Models\Outgoing;
use DateTime;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function index() {

        $boletoAtrasados = Outgoing::where('vencimento', '<', date('Y-m-d H:i:s'))
                                    ->where('paga', 0)->get();
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

    public function boletosAtrasados(){

        $boletoAtrasados = Outgoing::where('vencimento', '<', date('Y-m-d H:i:s'))
                                    ->where('paga', 0)->get();
        // dd($boletoAtrasados->vencimento);

        return view('boletosAtrasados', ['boletoAtrasados' => $boletoAtrasados]);
    }

    public function trataData($data) {
        return  date("d/m/Y", strtotime($data));
    }

    public function calcularData($data){
        $dt = new DateTime($data);
        $dataAtual = new DateTime();
        $diff = $dt->diff($dataAtual);
        $numDias = $diff->days;

        return $numDias;
    }
}
