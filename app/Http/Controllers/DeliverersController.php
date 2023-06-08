<?php

namespace App\Http\Controllers;

use App\Models\Deliverer;
use Illuminate\Support\Facades\Auth;

class DeliverersController extends Controller
{

    public function show()
    {
        $userAccess = Auth::user()->access;
        $deliverers = Deliverer::where('status', 1)->get();
        $arrayDeliverers = [];

        foreach ($deliverers as $deliverer) {
            $arrayDeliverers[] = [
                'id' => $deliverer->id,
                'name' => $deliverer->name,
                'pix' => $deliverer->pix,
                'cpf_or_cnpj' => $deliverer->cpf_or_cnpj,
                'status' => $deliverer->status,
                'created_at' => $deliverer->created_at
            ];
        }

        return view('deliverers/deliverer-management', ['deliverers' => $arrayDeliverers, 'userAccess' =>  $userAccess]);
    }

    public function showRemoved()
    {
        $userAccess = Auth::user()->access;
        $deliverers = Deliverer::where('status', 0)->get();
        $arrayDeliverers = [];

        foreach ($deliverers as $deliverer) {
            $arrayDeliverers[] = [
                'id' => $deliverer->id,
                'name' => $deliverer->name,
                'pix' => $deliverer->pix,
                'cpf_or_cnpj' => $deliverer->cpf_or_cnpj,
                'status' => $deliverer->status,
                'created_at' => $deliverer->created_at
            ];
        }

        return view('deliverers/deliverer-management-removed', ['deliverers' => $arrayDeliverers, 'userAccess' =>  $userAccess]);
    }

    public function create()
    {
        if (Auth::user() && Auth::user()->access !== 'admin') {
            return redirect('/dashboard');
        }
        return view('deliverers.register');
    }


    public function store()
    {
        // Verificar se o usuário tem acesso de administrador
        if (Auth::user() && Auth::user()->access !== 'admin') {
            return redirect('/dashboard');
        }

        dd('passei aqui');

        // Validação dos atributos
        $attributes = request()->validate([
            'name' => ['required'],
            'pix' => [],
            'cpf_or_cnpj' => ['required'],
        ]);


        $deliverer = Deliverer::firstOrCreate(['cpf_or_cnpj' => $attributes['cpf_or_cnpj']]);

        if ($deliverer->wasRecentlyCreated) {
            return response()->json(['success' => 'Entregador Cadastrado.']);
        } else {
            return response()->json(['error' => 'Entregador já existe, verifique nas inativas e reative.']);
        }
    }


    public function update($id)
    {
        $delivererEdit = Deliverer::where('id', $id)->first();
        return view('deliverers/deliverer-management-update', ['delivererEdit' => $delivererEdit]);
    }

    public function updateDeliverer($id)
    {
        $attributes = request()->validate([
            'name' => ['required', 'max:50'],
            'pix' => [],
            'cpf_or_cnpj' => ['required', 'max:14'],

        ]);

        Deliverer::where('id', $id)
            ->update([
                'name'    => $attributes['name'],
                'pix' => $attributes['pix'],
                'cpf_or_cnpj' => $attributes['cpf_or_cnpj']
            ]);


        return redirect('/deliverer-management')->with('success', 'Alteração realizada!');
    }

    public function destroy($id)
    {
        Deliverer::where('id', $id)
            ->update([
                'status' => false,
            ]);
    }

    public function recover($id)
    {
        Deliverer::where('id', $id)
            ->update([
                'status' => true,
            ]);
    }
}
