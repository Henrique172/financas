
@extends('adminlte::page')

@section('title', 'Despesas')

@section('content_header')
    <h1>Minhas despesas <a href="{{route('outgoingsCreate')}}" class="btn btn-sm btn-danger">Nova despesa</a></h1>
@endsection

@section('content')
<div class="input-group">
    <form id="searchForm" action="outgoing" method="GET">
        <input class="form-control form-control-sm" type="month" name="data" onchange="submitForm()">
        <div class="input-group-append" style="padding: 10px">
            <input type="hidden" name="selectedDate" id="selectedDate" value="">

            {{-- <input type="submit" class="btn btn-warning btn-sm" value="Pesquisar"/> --}}
        </div>
    </form>
</div>


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

        @if (count($outgoings) > 0)

            {{-- <div class="ml-auto p-2">
                <form action="{{route('outgoingsSearch')}}" method="get" class="for form inline">
                    @csrf
                    <input type="text" name="filter" placeholder="Buscar por Despesa" class="form-control" required>
                    <span class="mt-2"><button type="submit" class="btn btn-info mt-2 align-right">Pesquisar</button>
                </form>
            </div><br><br> --}}

            <h5>Buscar por categoria</h5>

            {{-- <div class="ml-auto p-2">
                <form action="{{route('outgoingsSearchCategory')}}" method="post" class="for form inline">
                    @csrf
                    <div class="form-group">
                            <div class="row">
                                <label class="col-sm-2 control-label">Categoria</label>
                                <div class="controls">
                                    <select name="category" id="category">
                                        @foreach($categoryes as $c)
                                        <option  value="{{$c->id}}">{{$c->ds_nome}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    <span class="mt-2"><button type="submit" class="btn btn-info mt-2 align-right">Pesquisar</button>
                </form>
            </div> --}}

                <div class="card">
                    <div class="card-body">

                        <table class="table table-hover">
                            <div style="padding: 10px; display: flex; justify-content: space-between;">
                                <div style="float: left;">
                                    <b>Despesa Total:</b><strong style="color: red">{{' R$ '.number_format($despesaTotal, 2, ',', '.')}}</strong>
                                </div>
                                <div style="float: left;">
                                    <b>Não Paga:</b><strong style="color: rgb(151, 0, 156)">{{' R$ '.number_format($naoPago, 2, ',', '.')}}</strong>
                                </div>
                                <div style="float: center;">
                                    <b>TOTAL MENSAL:</b><strong style="color: {{ $totalMensal > 0 ? 'rgb(0, 151, 20)' : 'red' }}">{{' R$ '.number_format($totalMensal, 2, ',', '.')}}</strong>
                                    {{-- <b>TOTAL MENSAL:</b><strong style="color: rgb(7, 17, 155)">{{' R$ '.number_format($totalMensal, 2, ',', '.')}}</strong> --}}
                                </div>
                                <div style="float: right;">
                                    <b>Receita Mensal:</b><strong style="color: rgb(0, 151, 20)">{{' R$ '.number_format($receita, 2, ',', '.')}}</strong>
                                </div>
                            </div>


                            <thead>
                                <tr>
                                    <th>Categoria</th>
                                    <th>Data</th>
                                    <th>Descrição</th>
                                    <th>Valor</th>
                                    <th>Vencimento</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($outgoings as $outgoing )
                                    <?php
                                        $outgoing->created = date('d/m/Y', strtotime($outgoing->created));
                                        $outgoing->vencimento = date('d/m/Y', strtotime($outgoing->vencimento));
                                    ?>
                                    <tr @if($outgoing->paga === 1) class="btn-success"@endif>
                                        <td>@if($outgoing->paga === 1)<del>{{$outgoing->category}}<del> @else {{$outgoing->category}} @endif</td>
                                            <td>@if($outgoing->paga === 1)<del>{{$outgoing->created}}<del> @else {{$outgoing->created}} @endif</td>
                                            <td>@if($outgoing->paga === 1)<del>{{$outgoing->description}}<del> @else {{$outgoing->description}} @endif </td>
                                            <td>@if($outgoing->paga === 1)<del>{{'R$ '.number_format($outgoing->value, 2, ',', '.')}}</del> @else {{'R$ '.number_format($outgoing->value, 2, ',', '.')}} @endif </td>
                                            <td>@if($outgoing->paga === 1) <del>{{$outgoing->vencimento}}</del> @else {{$outgoing->vencimento}} @endif </td>
                                            <td>
                                                <a href="{{ route('outgoingsEdit', ['id' => $outgoing->id]) }}" class="btn btn-sm btn-info">Editar</a>

                                                <form class="d-inline" method="POST" action="{{route('outgoingsDestroy', ['id' => $outgoing->id])}}" onsubmit="return confirm('tem certeza que deseja excluir esta despesa?')">
                                                    @method('DELETE')
                                                    @csrf
                                                    <button class="btn btn-sm btn-danger">Excluir</button>
                                                </form>
                                                <form class="d-inline" method="POST" action="{{route('outgoingsPay', ['id' => $outgoing->id])}}">
                                                    @method('PUT')
                                                    @csrf
                                                    <button class="btn btn-sm btn-info">@if($outgoing->paga === 1)marcar como não paga @else marcar como paga @endif </button>
                                                </form>

                                            </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                            <h4 style="text-align: center; color: #ccc; ">Você ainda não tem nenhuma despesa para ser listada nesse mês de {{ $dataConsulta }}, clique em nova despesa logo acima!</h4>
        @endif
                </div>
            </div>
            {{$outgoings->links()}}

@endsection


<style>
   .data{
    background-color: red
   }
</style>
<script>
    function submitForm() {
        document.getElementById('searchForm').submit();
    }
</script>
