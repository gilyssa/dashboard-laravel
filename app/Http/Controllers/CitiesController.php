<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Support\Facades\Auth;

class CitiesController extends Controller
{

    public function show()
    {   
        $userAccess = Auth::user()->access;
        $cities = City::where('status', 1)->get();
        $arrayCities = [];
    
        foreach ($cities as $city) {
            $arrayCities[] = [
                'id' => $city->id,
                'name' => $city->name,
                'status' => $city->status,
                'created_at' => $city->created_at
            ];
        }
        
        return view('cities/city-management', ['cities' => $arrayCities, 'userAccess' =>  $userAccess]);
    }
    
    public function showRemoved()
    {   
        $userAccess = Auth::user()->access;
        $cities = City::where('status', 0)->get();
        $arrayCities = [];
    
        foreach ($cities as $city) {
            $arrayCities[] = [
                'id' => $city->id,
                'name' => $city->name,
                'status' => $city->status,
                'created_at' => $city->created_at
            ];
        }
        
        return view('cities/city-management-removed', ['cities' => $arrayCities, 'userAccess' =>  $userAccess]);
    }
    
    public function create()
    {
        if (Auth::user() && Auth::user()->access !== 'admin') {
            return redirect('/dashboard');
        }
        return view('cities.register');
    }
    
    
    public function store()
    {
        // Verificar se o usuário tem acesso de administrador
        if (Auth::user() && Auth::user()->access !== 'admin') {
            return redirect('/dashboard');
        }
    
        // Validação dos atributos
        $attributes = request()->validate([
            'name' => ['required'],
        ]);
        
        $city = City::firstOrCreate(['name' => $attributes['name']]);
    
        if ($city->wasRecentlyCreated) {
            return response()->json(['success' => 'Cidade Cadastrada.']);
        } else {
            return response()->json(['error' => 'Cidade já existe, verifique nas inativas e reative.']);
        }
    }    
    

    public function update($id)
    {
        $cityEdit = City::where('id', $id)->first();
        return view('cities/city-management-update', ['cityEdit' => $cityEdit]);
    }

    public function updateCity($id)
    {
        $attributes = request()->validate([
            'name' => ['required', 'max:50'],
        ]);
    

        
        City::where('id', $id)
        ->update([
            'name'    => $attributes['name'],
        ]);
    
    
        return redirect('/city-management')->with('success','Alteração realizada!');
    }

    public function destroy($id){
        City::where('id', $id)
        ->update([
            'status'=> false,
        ]);
    }

    public function recover($id){
        City::where('id', $id)
        ->update([
            'status'=> true,
        ]);
    }
}
