<?php

namespace App\Http\Controllers;

use App\Models\Deliverer;
use Illuminate\Support\Facades\Auth;
use App\Validators\CPForCNPJValidator;

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
                'cnpj_or_cpf' => $deliverer->cnpj_or_cpf,
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
                'cnpj_or_cpf' => $deliverer->cnpj_or_cpf,
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

        // Validação dos atributos
        $attributes = request()->validate([
            'name' => ['required'],
            'pix' => [],
            'cnpj_or_cpf' => ['required'],
        ]);

        // Verificar se o cnpj_or_cpf já existe no banco de dados
        $existingDelivererDocument = Deliverer::where('cnpj_or_cpf', $attributes['cnpj_or_cpf'])->first();
        $existingDelivererName = Deliverer::where('name', $attributes['name'])->first();

        if ($existingDelivererDocument || $existingDelivererName) {
            return response()->json(['error' => 'Entregador já existe, verifique se o nome ou o documento já não está nos entregadores inativos']);
        }

        // Validar o CPF ou CNPJ
        if (!CPForCNPJValidator::validateDocument($attributes['cnpj_or_cpf'])) {
            return response()->json(['error' => 'CPF ou CNPJ inválido']);
        }

        //dd($attributes);
        Deliverer::create($attributes);
        return response()->json(['success' => 'Entregador Cadastrado.']);
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
            'cnpj_or_cpf' => ['required'],
        ]);

        // Verificar se o nome ou CPF/CNPJ já existem, excluindo o entregador atual
        $existingDeliverer = Deliverer::where(function ($query) use ($attributes) {
            $query->where('name', $attributes['name'])
                ->orWhere('cnpj_or_cpf', $attributes['cnpj_or_cpf']);
        })
            ->where('id', '<>', $id)
            ->first();

        if ($existingDeliverer) {
            return back()->withErrors(['error' => 'Entregador já existe, verifique se o nome ou o documento já não está nos entregadores inativos']);
        }

        // Validar o CPF ou CNPJ
        if (!CPForCNPJValidator::validateDocument($attributes['cnpj_or_cpf'])) {
            return back()->withErrors(['error' => 'CPF ou CNPJ inválido.']);
        }

        Deliverer::where('id', $id)->update([
            'name' => $attributes['name'],
            'pix' => $attributes['pix'],
            'cnpj_or_cpf' => $attributes['cnpj_or_cpf']
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
