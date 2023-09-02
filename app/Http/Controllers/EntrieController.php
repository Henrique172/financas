<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Categoryes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Entrie;
use App\Traits\DateConversionTrait;



class EntrieController extends Controller
{
    use DateConversionTrait;



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

        $dataExtenso = $this->$data? $this->converterDataExtenso($this->$data, 'Y-m') : $this->converterDataExtenso(date('Y-m'), 'Y-m');

        $this->userLogged = Auth::id();
        $categoryes = Categoryes::all();

        $entries = Entrie::orderBy('id', 'DESC')
        ->where('id_user', $this->userLogged)
        ->where('mesAno', isset($this->$data)? $this->$data : date('Y-m'))
        ->paginate(5);
        return view('entries.entries', [
            'entries' => $entries,
            'categoryes' => $categoryes,
            'dataExtenso' => $dataExtenso,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   $this->userLogged = Auth::id();
        return view('entries.create',[
            'user_id' => $this->userLogged
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
            'description',
            'value',
            'mesAno'

        ]);

        $validator = $this->validator($data);
        if ($data['value'] < 0) {
            $validator->errors()->add('value', 'o valor não pode ser negativo');
            return redirect()->route('entriesCreate')->withErrors($validator)->withInput();
        }

        if ($validator->fails()) {
            return redirect()->route('entriesCreate')->withErrors($validator)->withInput();
        }

        $entrie = new Entrie;
        $entrie->id_user = $data['id_user'];
        $entrie->created = date('Y-m-d h:i:s');
        $entrie->description = $data['description'];
        $entrie->value = $data['value'];
        $entrie->mesAno = $data['mesAno'];
        $entrie->save();

        return redirect()->route('entriesIndex')->with('warning', 'Receita cadastrada com sucesso');
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
        $entrie = Entrie::find($id);

        if ($entrie == null) {
            return redirect()->route('entriesIndex')->withErrors('Entrada inexistente');;
        } elseif($entrie->id_user !== $this->userLogged) {
            return redirect()->route('entriesIndex')->withErrors('Essa entrada não é sua, impossivel editar!');
        } else {
            return view('entries.edit', [
                'entrie' => $entrie
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
        $this->userLogged = Auth::id();

        $data = $request->only([
            'description',
            'value'

        ]);

        $validator = $this->validator($data);

        if ($data['value'] < 0) {
            $validator->errors()->add('value', 'o valor não pode ser negativo');
            return redirect()->route('entriesEdit', ['id' => $id])->withErrors($validator)->withInput();
        }

        if ($validator->fails()) {
            return redirect()->route('entriesEdit', ['id' => $id])->withErrors($validator);
        }

        $entrie = Entrie::find($id);
        if ($entrie == null) {
            return redirect()->route('entriesIndex')->withErrors('Entrada inexistente');
        } elseif($entrie->id_user !== $this->userLogged) {
            return redirect()->route('entriesIndex')->withErrors('Essa entrada não é sua, impossivel editar!');
        } else {
            /*$entrie->category = $data['category'];
            $entrie->description = $data['description'];
            $entrie->value = $data['value'];
            $entrie->save();*/
            Entrie::find($id)->update([
                'description' => $data['description'],
                'value' => $data['value']
            ]);
            return redirect()->route('entriesIndex')->with('warning', 'Entrada alterada com sucesso');
        }



    }

    public function search(Request $request)
    {
        $this->userLogged = Auth::id();

        $search = $request->filter;
        $results = Entrie::where([['description', 'like', '%'.$search.'%']])->where('id_user', $this->userLogged)->paginate(5);

       return view('entries.search', [
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

        $results = Entrie::where('category', $search['category'])->where('id_user', $this->userLogged)->paginate(5);

       return view('entries.searchCategory', [
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
        $entrie = Entrie::find($id);
        if ($entrie == null) {
            return redirect()->route('entriesIndex')->withErrors('entrada inexistente, nada foi excluído');
        } elseif($entrie->id_user !== $this->userLogged) {
            return redirect()->route('entriesIndex')->withErrors('Receita não pertence a você, nada deletado!');
        } else {
            //$entrie->delete();

            Entrie::find($id)->delete();

            return redirect()->route('entriesIndex')->with('warning', 'A entrada foi excluída');;
        }

    }

    protected function validator(array $data) {
        return Validator::make($data, [
          'description' => ['required', 'string', 'max:191'],
          'value' => ['required']
        ]);
    }
}
