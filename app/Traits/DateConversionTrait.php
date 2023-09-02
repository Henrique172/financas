<?php

namespace App\Traits;

use Illuminate\Support\Carbon;

trait DateConversionTrait
{
    protected $mesesExtenso = [
    1 => 'janeiro', 2 => 'fevereiro', 3 => 'março', 4 => 'abril', 5 => 'maio', 6 => 'junho',
    7 => 'julho', 8 => 'agosto', 9 => 'setembro', 10 => 'outubro', 11 => 'novembro', 12 => 'dezembro'
];

public function converterDataExtenso($data, $formatoOriginal)
{
    $dataCarbon = Carbon::createFromFormat($formatoOriginal, $data);

        if ($formatoOriginal === 'Y-m') {
            $mes = $this->mesesExtenso[$dataCarbon->month];
            $ano = $dataCarbon->year;

            return "{$mes} de {$ano}";
        } else {
            $dia = $dataCarbon->day;
            $mes = $this->mesesExtenso[$dataCarbon->month];
            $ano = $dataCarbon->year;

            return "{$dia} de {$mes} de {$ano}";
        }
}
}
