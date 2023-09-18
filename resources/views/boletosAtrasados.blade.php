
@extends('adminlte::page')

@section('title', 'Despesas')

@section('content_header')
    <h1>Boletos Atrasados</h1>
@endsection

@section('content')


    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
            <h5>
                <i class="icon fas fa-ban"></i>
                Erro!
            </h5>
                @foreach ($errors->all() as $error)
                    <li>{{$error}}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-info">

            {{session('warning')}}

        </div>
    @endif

        {{-- <table>
            <tr>
                <th>#</th>
                <th>Descrição</th>
                <th>Valor</th>
                <th>Data</th>
                <th>Dias Atrasados</th>
            </tr>
            @foreach ($boletoAtrasados as $b )
            <tr>
                    <td> - </td>
                    <td>{{ $b->value }}</td>
                    <td>{{ $b->description }}</td>
                    <td>{{ $b->vencimento }}</td>
                </tr>
                @endforeach
        </table> --}}

        <div class="card">
            <div class="card-body">

                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Descrição</th>
                            <th>Valor</th>
                            <th>Data</th>
                            <th>Dias Atrasados</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i = 0;@endphp
                        @foreach ($boletoAtrasados as $b )
                        @php
                             $i ++;
                             $colorApp = '';
                            //  dd('teste');
                             $dias = app('App\Http\Controllers\HomeController')->calcularData($b->vencimento);
                            if($dias >= 1 && $dias <= 5){
                                $colorApp = '#c2bf15';
                            }else if($dias >= 6 && $dias <= 10){
                                $colorApp = '#c26c15';
                            }else if($dias >= 11 && $dias <= 15){
                                $colorApp = '#c23815';
                            }else if($dias >= 20 && $dias <= 30){
                                $colorApp = '#c21515';
                            }

                        @endphp
                        <tr style="background-color:{{ $colorApp}}" @if($b->paga === 1)class="btn-success"@endif>
                            <td>{{ $i  }}</td>
                                <td>{{ $b->description }}</td>
                                <td>{{ $b->value }}</td>
                                <td>{{ app('App\Http\Controllers\HomeController')->trataData($b->vencimento)}}</td>
                                <td>{{ app('App\Http\Controllers\HomeController')->calcularData($b->vencimento) }} - Dias</td>

                                {{-- <td>{{ call_user_func([$HomeController, 'calcularData'], $b->vencimento) }}</td> --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
        </div>
    </div>

@endsection


<style>
   .data{
    background-color: red
   }
</style>
