@extends('adminlte::page')


@section('title', 'Perfil do Usuário')

@section('content_header')
<h1>Categorias</h1>
@endsection

@section('content')


@if (session('warning'))
<div class="alert alert-success">

    {{session('warning')}}

</div>
@endif
<table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Nome</th>
      </tr>
    </thead>
    <tbody>
        @foreach ($categoryes as $c )
      <tr>
        <td>{{$c->id}}</td>
        <td>{{$c->ds_nome}}</td>
      </tr>
      @endforeach
    </tbody>
  </table>


<br/>
  <div class="card">
    <h3>Adicione Categorias</h3>
    <div class="card-body">
        <form action="{{route('categoryUpdate')}}" method="POST" class="form-horizontal">
        @method('PUT')
        @csrf


        <div class="form-group">
            <div class="row">
                <label class="col-sm-2 control-label">Categoria</label>
                <div class="col-sm-8">
                    <input type="text" name="ds_nome"  class="form-control @error('category') is-invalid @enderror">
                </div>
            </div>
        </div>


        <div class="form-group row">
             <label class="col-sm-2 col-form-label"></label>
                <div class="col-sm-10">
                    <input type="submit" value="Salvar" class="btn btn-success">
                </div>
        </div>

    </form>
    </div>
</div>

@endsection
<style>
    table {
    border-collapse: collapse;
    width: 95%;
    max-width: 95%;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    background-color: #ffffff;
  }
  th, td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #dddddd;
  }
  th {
    background-color: #f2f2f2;
  }
  tr:nth-child(even) {
    background-color: #f2f2f2;
  }
  tr:hover {
    background-color: #e0e0e0;
    transition: background-color 0.3s ease-in-out;
  }
  b{
    color: red;
  }

</style>
