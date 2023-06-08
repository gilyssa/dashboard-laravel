<?php

namespace App\Http\Controllers;

use App\Models\Enterprise;
use Illuminate\Support\Facades\Auth;

class EnterprisesController extends Controller
{

    public function show()
    {
        $userAccess = Auth::user()->access;
        $enterprises = Enterprise::where('status', 1)->get();
        $arrayEnterprises = [];

        foreach ($enterprises as $enterprise) {
            $arrayEnterprises[] = [
                'id' => $enterprise->id,
                'name' => $enterprise->name,
                'status' => $enterprise->status,
                'created_at' => $enterprise->created_at
            ];
        }

        return view('enterprises/enterprise-management', ['enterprises' => $arrayEnterprises, 'userAccess' =>  $userAccess]);
    }

    public function showRemoved()
    {
        $userAccess = Auth::user()->access;
        $enterprises = Enterprise::where('status', 0)->get();
        $arrayEnterprises = [];

        foreach ($enterprises as $enterprise) {
            $arrayEnterprises[] = [
                'id' => $enterprise->id,
                'name' => $enterprise->name,
                'status' => $enterprise->status,
                'created_at' => $enterprise->created_at
            ];
        }

        return view('enterprises/enterprise-management-removed', ['enterprises' => $arrayEnterprises, 'userAccess' =>  $userAccess]);
    }

    public function create()
    {
        if (Auth::user() && Auth::user()->access !== 'admin') {
            return redirect('/dashboard');
        }
        return view('enterprises.register');
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

        $enterprise = Enterprise::firstOrCreate(['name' => $attributes['name']]);

        if ($enterprise->wasRecentlyCreated) {
            return response()->json(['success' => 'Empresa Cadastrada.']);
        } else {
            return response()->json(['error' => 'Empresa já existe, verifique nas inativas e reative.']);
        }
    }


    public function update($id)
    {
        $enterpriseEdit = Enterprise::where('id', $id)->first();
        return view('enterprises/enterprise-management-update', ['enterpriseEdit' => $enterpriseEdit]);
    }

    public function updateEnterprise($id)
    {
        $attributes = request()->validate([
            'name' => ['required', 'max:50'],
        ]);

        Enterprise::where('id', $id)
            ->update([
                'name'    => $attributes['name'],
            ]);


        return redirect('/enterprise-management')->with('success', 'Alteração realizada!');
    }

    public function destroy($id)
    {
        Enterprise::where('id', $id)
            ->update([
                'status' => false,
            ]);
    }

    public function recover($id)
    {
        Enterprise::where('id', $id)
            ->update([
                'status' => true,
            ]);
    }
}
