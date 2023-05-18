<?php

namespace App\Http\Controllers;

use App\Models\PriceBand;
use Illuminate\Support\Facades\Auth;

class PriceBandsController extends Controller
{

    public function show()
    {   
        $userAccess = Auth::user()->access;
        $pricebands = PriceBand::where('status', 1)->get();
        $arrayPriceBands = [];
    
        foreach ($pricebands as $priceband) {
            $formattedValue = 'R$ ' . number_format($priceband->value, 2, ',', '.');
            $arrayPriceBands[] = [
                'id' => $priceband->id,
                'value' => $formattedValue,
                'status' => $priceband->status,
                'created_at' => $priceband->created_at
            ];
        }
        
        return view('pricebands/priceband-management', ['pricebands' => $arrayPriceBands, 'userAccess' =>  $userAccess]);
    }
    
    public function showRemoved()
    {   
        $userAccess = Auth::user()->access;
        $pricebands = PriceBand::where('status', 0)->get();
        $arrayPriceBands = [];
    
        foreach ($pricebands as $priceband) {
            $formattedValue = 'R$ ' . number_format($priceband->value, 2, ',', '.');
            $arrayPriceBands[] = [
                'id' => $priceband->id,
                'value' => $formattedValue,
                'status' => $priceband->status,
                'created_at' => $priceband->created_at
            ];
        }
        
        return view('pricebands/priceband-management-removed', ['pricebands' => $arrayPriceBands, 'userAccess' =>  $userAccess]);
    }
    
    public function create()
    {
        if (Auth::user() && Auth::user()->access !== 'admin') {
            return redirect('/dashboard');
        }
        return view('pricebands.register');
    }
    
    public function store()
    {
        // Verificar se o usuário tem acesso de administrador
        if (Auth::user() && Auth::user()->access !== 'admin') {
            return redirect('/dashboard');
        }
    
        // Validação dos atributos
        $attributes = request()->validate([
            'value' => ['required'],
        ]);
    
        $value = str_replace(',', '.', $attributes['value']);
    
        $priceBand = PriceBand::firstOrCreate(['value' => $value]);
    
        if ($priceBand->wasRecentlyCreated) {
            return response()->json(['success' => 'Faixa Cadastrada.']);
        } else {
            return response()->json(['error' => 'Faixa já existe, verifique nas inativas e reative.']);
        }
    }    
    

    public function update($id)
    {
        $priceBandEdit = PriceBand::where('id', $id)->first();
        return view('pricebands/priceband-management-update', ['priceBandEdit' => $priceBandEdit]);
    }

    public function updatePriceBand($id)
    {
        $attributes = request()->validate([
            'value' => ['required'],
        ]);
    

        
        PriceBand::where('id', $id)
        ->update([
            'value'    => $attributes['value'],
        ]);
    
    
        return redirect('/priceband-management')->with('success','Alteração realizada!');
    }

    public function destroy($id){
        PriceBand::where('id', $id)
        ->update([
            'status'=> false,
        ]);
    }

    public function recover($id){
        PriceBand::where('id', $id)
        ->update([
            'status'=> true,
        ]);
    }
}
