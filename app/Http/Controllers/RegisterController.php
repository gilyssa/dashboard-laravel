<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function create()
    {
        if (Auth::user() && Auth::user()->access !== 'admin') {
            return redirect('/dashboard');
        }
        return view('session.register');
    }
    
    public function store()
    {
        // Verificar se o usuário tem acesso de administrador
        if (Auth::user() && Auth::user()->access !== 'admin') {
            return redirect('/dashboard');
        }
    
        // Validação dos atributos
        $attributes = request()->validate([
            'name' => ['required', 'max:50'],
            'email' => ['required', 'email', 'max:50', Rule::unique('users', 'email')],
            'password' => ['required', 'min:5', 'max:20'],
            'access' => ['required'] // Certifique-se de incluir a validação para o campo 'access'
        ]);
    
        // Criar o hash da senha
        $attributes['password'] = bcrypt($attributes['password']);
    
        // Criação do usuário
        User::create($attributes);
    
        session()->flash('success', 'Usuário Cadastrado.');
    
        return redirect('/user-management'); // Redirecionar para a página desejada após o cadastro
    }
}
