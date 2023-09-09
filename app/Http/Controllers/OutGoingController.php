<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Categoryes;
use App\Models\Entrie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Outgoing;
use Carbon\Carbon;



class OutGoingController extends Controller

{

    protected $userLogged;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = '';
        if ($request->isMethod('get')) {
            $this->$data = $request->input('data');
        }
        // var_dump( date('Y-m'));
        $this->userLogged = Auth::id();
        $outgoings = Outgoing::orderBy('id', 'DESC')
        ->where('id_user', $this->userLogged)
        ->where('mesAno', isset($this->$data)? $this->$data : date('Y-m'))
        ->paginate(15);

        $categoriesWithOutgoings = Outgoing::with('Categoryes')->get();

        //    CALCULAR SOMENTES OS QUE NAO FORAM PAGOS
        $naoPagosAll = Outgoing::where('paga', 0)
        ->where('mesAno', isset($this->$data)? $this->$data : date('Y-m'))->get();
        $naoPago = 0;
        foreach($naoPagosAll as $calc){
            $naoPago += $calc->value;
        }

        //    CALCULAR SOMENTES OS QUE NAO FORAM PAGOS
        $categoryoutgoingsAll = Outgoing::where('paga', 1)
        ->where('mesAno', isset($this->$data)? $this->$data : date('Y-m'))->get();
        $despesaTotal = 0;
        foreach($categoryoutgoingsAll as $calc){
            $despesaTotal += $calc->value;
        }

        //    CALCULAR SOMENTES OS QUE FORAM PAGOS
        $categoryoutgoingsAllPaga = Outgoing::where('mesAno', isset($this->$data)? $this->$data : date('Y-m'))->get();
        $despesaTotalPaga = 0;
        foreach($categoryoutgoingsAllPaga as $calc){
            $despesaTotalPaga += $calc->value;
        }

        // $receita = Entrie::where('mesAno', isset($this->$data) ? $this->$data : date('Y-m'))->first();
        $receitas = Entrie::where('mesAno', isset($this->$data) ? $this->$data : date('Y-m'))->get();

        $totalReceitas = 0;
        foreach ($receitas as $receita) {
            $totalReceitas += $receita->value;
        }

        $formattedDate = $this->$data? Carbon::createFromFormat('Y-m', $this->$data)->format('F Y'): date('Y-m');

        $totalMensal = $totalReceitas - $despesaTotal;

        $categoryes = Categoryes::all();
        return view('outgoings.outgoings', [
            'outgoings' => $outgoings,
            'categoryes' => $categoryes,
            'despesaTotal' => $despesaTotalPaga,
            'dataConsulta' => $formattedDate,
            'receita' => $totalReceitas,
            'totalMensal' => $totalMensal,
            'naoPago' => $naoPago,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->userLogged = Auth::id();
        $categoryes = Categoryes::all();

        return view('outgoings.create',[
            'user_id' => $this->userLogged,
            'categoryes' => $categoryes
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->only([
            'id_user',
            'category',
            'mesAno',
            'description',
            'value',
            'vencimento'
        ]);

        $validator = $this->validator($data);

        if ($data['value'] < 0) {
            $validator->errors()->add('value', 'o valor não pode ser negativo');
            return redirect()->route('outgoingsCreate')->withErrors($validator)->withInput();
        }

        if ($data['vencimento'] < now()) {
            $validator->errors()->add('vencimento', 'a data não poder ser no passado e nem ultrapassar 12 meses!');
            return redirect()->route('outgoingsCreate')->withErrors($validator)->withInput();
        }

        if ($validator->fails()) {
            return redirect()->route('outgoingsCreate')->withErrors($validator)->withInput();
        }
        // dd($data);


        $outgoing = new Outgoing;
        $outgoing->id_user = $data['id_user'];
        $outgoing->category = $data['category'];
        $outgoing->created = date('Y-m-d h:i:s');
        $outgoing->mesAno = $data['mesAno'];
        $outgoing->description = $data['description'];
        $outgoing->value = $data['value'];
        $outgoing->vencimento = date($data['vencimento']);
        $outgoing->save();

        return redirect()->route('outgoingsIndex')->with('warning', 'Despesa criada com sucesso');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->userLogged = Auth::id();
        $outgoing = Outgoing::find($id);
        $categoryes = Categoryes::all();


        if ($outgoing == null) {
            return redirect()->route('outgoingsIndex')->withErrors('Despesa inexistente');;
        } elseif($outgoing->id_user !== $this->userLogged) {
            return redirect()->route('outgoingsIndex')->withErrors('Essa Despesa não é sua, impossivel editar!');
        } else {
            return view('outgoings.edit', [
                'outgoing' => $outgoing,
                'categoryes' => $categoryes,
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->only([
            'category',
            'description',
            'value',
            'vencimento'

        ]);

        $validator = $this->validator($data);

        if ($data['value'] < 0) {
            $validator->errors()->add('value', 'o valor não pode ser negativo');
            return redirect()->route('outgoingsCreate')->withErrors($validator)->withInput();
        }

        if ($data['vencimento'] < now()) {
            $validator->errors()->add('vencimento', 'a data não poder ser no passado e nem ultrapassar 12 meses!');
            return redirect()->route('outgoingsCreate')->withErrors($validator)->withInput();
        }

        if ($validator->fails()) {
            return redirect()->route('outgoingsEdit', ['id' => $id])->withErrors($validator);
        }

        $outgoing = Outgoing::find($id);
        if ($outgoing == null) {
            return redirect()->route('outgoingsIndex')->withErrors('Despesa inexistente');
        } else {
            $outgoing->category = $data['category'];
            $outgoing->description = $data['description'];
            $outgoing->value = $data['value'];
            $outgoing->vencimento = $data['vencimento'];
            $outgoing->save();
            return redirect()->route('outgoingsIndex')->with('warning', 'Despesa alterada com sucesso');;
        }
    }

    public function pay($id) {
        // dd($id);
        $this->userLogged = Auth::id();
        $outgoing = Outgoing::find($id);
        if ($outgoing == null) {
            return redirect()->route('outgoingsIndex')->withErrors('Despesa inexistente');
        } elseif($outgoing->id_user !== $this->userLogged) {
            return redirect()->route('outgoingsIndex')->withErrors('Essa Despesa não é sua, impossivel editar!');
        } else {
            if ($outgoing->paga == 1) {
                $outgoing->paga = 0;
                $outgoing->save();
            } else {
                $outgoing->paga = 1;
                $outgoing->save();
            }
            return redirect()->route('outgoingsIndex');
        }
    }

    public function search(Request $request)
    {
        $this->userLogged = Auth::id();

        $search = $request->filter;
        $results = Outgoing::where([['description', 'like', '%'.$search.'%']])->where('id_user', $this->userLogged)->paginate(5);

       return view('outgoings.search', [
        'results' => $results,
        'search' => $search
    ]);
    }

    public function searchCategory(Request $request)
    {
        $this->userLogged = Auth::id();

        $search = $request->only([
            'category'
        ]);


        $results = Outgoing::where('category', $search['category'])->where('id_user', $this->userLogged)->paginate(5);

       return view('outgoings.searchCategory', [
        'results' => $results,
        'search' => $search
    ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->userLogged = Auth::id();
        $outgoing = Outgoing::find($id);
        if ($outgoing == null) {
            return redirect()->route('outgoingsIndex')->withErrors('Despesa inexistente, nada foi excluído');
        } elseif($outgoing->id_user !== $this->userLogged) {
            return redirect()->route('outgoingsIndex')->withErrors('Despesa não pertence a você, nada deletado!');
        } else {
            $outgoing->delete();

            return redirect()->route('outgoingsIndex')->with('warning', 'A Despesa foi Excluída!');
        }
    }

    protected function validator(array $data) {
        return Validator::make($data, [
          'category' => ['required', 'string'],
          'description' => ['required', 'string', 'max:191'],
          'value' => ['required'],
          'vencimento' => ['required', 'date']
        ]);
    }
}
