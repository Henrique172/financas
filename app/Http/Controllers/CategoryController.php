<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Categoryes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class CategoryController extends Controller
{

    protected $userLogged;

    public function index() {
        // $this->userLogged = Auth::id();

        $categoryes = Categoryes::all();
        // dd('chegou na categorie', $categoryes);

        if ($categoryes) {
            return view('categoryes/category', [
                'categoryes' => $categoryes
            ]);
        } else {
            return redirect()->route('entriesIndex');
        }

    }

    public function update(Request $request) {

        $data = $request->only([
            'id',
            'ds_nome',

        ]);
        $category = new Categoryes();
        $category->ds_nome = $data['ds_nome'];
        $category->save();

        return redirect()->route('category');

        }


    protected function validator(array $data) {
        return Validator::make($data, [
          'name' => ['required', 'string', 'max:100'],
          //'email' => ['required', 'string', 'email', 'max:100', 'unique:users'],
          //'password' => ['required', 'string', 'min:4', 'confirmed'],
        ]);
    }
}
