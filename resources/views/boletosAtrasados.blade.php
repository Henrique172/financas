
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
                        @foreach ($boletoAtrasados as $b )
                            <tr @if($b->paga === 1) class="btn-success"@endif>
                                <td> - </td>
                                <td>{{ $b->description }}</td>
                                <td>{{ $b->value }}</td>
                                <td>{{ $b->vencimento, 'd/m/Y' }}</td>
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
